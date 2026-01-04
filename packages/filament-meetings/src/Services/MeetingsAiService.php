<?php

namespace Haida\FilamentMeetings\Services;

use Filamat\IamSuite\Services\AuditService;
use Haida\FilamentAiCore\DataTransferObjects\AiResult;
use Haida\FilamentAiCore\Services\AiPolicyService;
use Haida\FilamentAiCore\Services\AiService;
use Haida\FilamentMeetings\DTOs\MeetingDto;
use Haida\FilamentMeetings\Events\MeetingAiAgendaGenerated;
use Haida\FilamentMeetings\Events\MeetingAiMinutesGenerated;
use Haida\FilamentMeetings\Jobs\GenerateMeetingAgendaJob;
use Haida\FilamentMeetings\Jobs\GenerateMeetingMinutesJob;
use Haida\FilamentMeetings\Jobs\GenerateMeetingRecapJob;
use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Models\MeetingActionItem;
use Haida\FilamentMeetings\Models\MeetingAgendaItem;
use Haida\FilamentMeetings\Models\MeetingAiRun;
use Haida\FilamentMeetings\Models\MeetingMinute;
use Haida\FilamentMeetings\Models\MeetingTemplate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MeetingsAiService
{
    public function __construct(
        protected AiService $aiService,
        protected AiPolicyService $policyService,
        protected MeetingConsentService $consentService,
        protected MeetingTranscriptService $transcriptService,
        protected AuditService $auditService,
        protected MeetingFollowUpService $followUpService,
    ) {}

    public function shouldQueue(): bool
    {
        return (bool) config('filament-meetings.ai.queue.enabled', false);
    }

    public function queueAgenda(Meeting $meeting, ?Authenticatable $actor = null): void
    {
        $job = new GenerateMeetingAgendaJob($meeting->getKey(), $meeting->tenant_id, $actor?->getAuthIdentifier());
        $this->dispatchJob($job);
    }

    public function queueMinutes(Meeting $meeting, ?Authenticatable $actor = null): void
    {
        $job = new GenerateMeetingMinutesJob($meeting->getKey(), $meeting->tenant_id, $actor?->getAuthIdentifier());
        $this->dispatchJob($job);
    }

    public function queueRecap(Meeting $meeting, ?Authenticatable $actor = null): void
    {
        $job = new GenerateMeetingRecapJob($meeting->getKey(), $meeting->tenant_id, $actor?->getAuthIdentifier());
        $this->dispatchJob($job);
    }

    public function canGenerateAgenda(Meeting $meeting): ?string
    {
        $policy = $this->policyService->resolvePolicy($meeting->tenant);
        if (! $meeting->ai_enabled || ! ($policy['enabled'] ?? false)) {
            return 'هوش مصنوعی برای این جلسه غیرفعال است.';
        }

        return null;
    }

    public function canGenerateMinutes(Meeting $meeting, ?Authenticatable $actor = null): ?string
    {
        $message = $this->canGenerateAgenda($meeting);
        if ($message) {
            return $message;
        }

        if ($this->consentService->isConsentRequired($meeting) && ! $this->consentService->isConsentSatisfied($meeting, $actor)) {
            return 'بدون ثبت رضایت امکان تولید صورتجلسه وجود ندارد.';
        }

        return null;
    }

    public function canGenerateRecap(Meeting $meeting): ?string
    {
        return $this->canGenerateAgenda($meeting);
    }

    /**
     * @return array{ok: bool, message?: string, agenda?: array<int, array<string, mixed>>, result?: AiResult}
     */
    public function generateAgenda(Meeting $meeting, ?Authenticatable $actor = null): array
    {
        $message = $this->canGenerateAgenda($meeting);
        if ($message) {
            return ['ok' => false, 'message' => $message];
        }

        $meeting->loadMissing(['organizer', 'attendees.user', 'agendaItems', 'actionItems']);

        $constraints = [
            'max_items' => (int) config('filament-meetings.ai.agenda.max_items', 12),
            'default_timebox_minutes' => (int) config('filament-meetings.ai.agenda.default_timebox_minutes', 10),
        ];

        $context = $this->buildMeetingContext($meeting);

        $result = $this->aiService->generateAgenda('meetings', 'agenda', $context, $constraints, [], [], $actor);
        if (! $result->ok) {
            return ['ok' => false, 'message' => $result->error ?: 'درخواست ناموفق بود.', 'result' => $result];
        }

        $agenda = Arr::wrap($result->output_json['agenda'] ?? []);

        DB::transaction(function () use ($meeting, $agenda, $result, $actor) {
            $start = (int) MeetingAgendaItem::query()
                ->where('meeting_id', $meeting->getKey())
                ->max('sort_order');

            $order = $start;
            foreach ($agenda as $item) {
                $order++;
                MeetingAgendaItem::query()->create([
                    'tenant_id' => $meeting->tenant_id,
                    'meeting_id' => $meeting->getKey(),
                    'sort_order' => $order,
                    'title' => (string) ($item['title'] ?? 'موضوع بدون عنوان'),
                    'description' => $item['description'] ?? null,
                    'timebox_minutes' => $item['timebox_minutes'] ?? null,
                    'owner_id' => $item['owner_id'] ?? null,
                ]);
            }

            MeetingAiRun::query()->create([
                'tenant_id' => $meeting->tenant_id,
                'meeting_id' => $meeting->getKey(),
                'action_type' => 'agenda',
                'provider' => $result->provider,
                'output_hash' => $this->hashResult($result),
                'created_by' => $actor?->getAuthIdentifier(),
            ]);
        });

        $this->auditService->log('meetings.ai.agenda.generated', $meeting, [
            'count' => count($agenda),
        ], $actor);

        event(new MeetingAiAgendaGenerated(MeetingDto::fromModel($meeting), $agenda));

        return ['ok' => true, 'agenda' => $agenda, 'result' => $result];
    }

    /**
     * @return array{ok: bool, message?: string, minutes?: MeetingMinute, result?: AiResult}
     */
    public function generateMinutes(Meeting $meeting, ?Authenticatable $actor = null): array
    {
        $message = $this->canGenerateMinutes($meeting, $actor);
        if ($message) {
            return ['ok' => false, 'message' => $message];
        }

        $meeting->loadMissing(['organizer', 'attendees.user', 'agendaItems', 'notes', 'minutes', 'actionItems']);

        $context = $this->buildMeetingContext($meeting);
        $transcriptPayload = $this->transcriptService->buildTranscriptPayload($meeting);

        if (! $transcriptPayload['has_content']) {
            $transcriptPayload['text'] = $meeting->notes?->content_longtext ?: $this->renderAgendaText($meeting);
            $transcriptPayload['source'] = $transcriptPayload['text'] ? 'notes' : 'agenda';
        }

        $context['minutes_schema'] = $this->resolveMinutesSchema($meeting);

        $result = $this->aiService->generateMinutes('meetings', 'minutes', $transcriptPayload, $context, [], [], $actor);
        if (! $result->ok) {
            $draft = $this->createManualMinutesDraft($meeting, $context, $transcriptPayload, $actor);

            return [
                'ok' => false,
                'message' => $result->error ?: 'درخواست ناموفق بود.',
                'minutes' => $draft,
                'result' => $result,
            ];
        }

        $payload = (array) ($result->output_json ?? []);
        $minutes = DB::transaction(function () use ($meeting, $payload, $result, $actor) {
            $minute = MeetingMinute::query()->create([
                'tenant_id' => $meeting->tenant_id,
                'meeting_id' => $meeting->getKey(),
                'overview_text' => (string) ($payload['overview'] ?? $payload['overview_text'] ?? ''),
                'keywords_json' => $payload['keywords'] ?? $payload['keywords_json'] ?? null,
                'outline_json' => $payload['outline'] ?? $payload['outline_json'] ?? null,
                'summary_markdown' => (string) ($payload['summary_markdown'] ?? $payload['summary'] ?? ''),
                'decisions_json' => $payload['decisions'] ?? $payload['decisions_json'] ?? null,
                'risks_json' => $payload['risks'] ?? $payload['risks_json'] ?? null,
            ]);

            $actionItems = Arr::wrap($payload['action_items'] ?? []);
            foreach ($actionItems as $item) {
                $title = (string) ($item['title'] ?? $item['action'] ?? 'اقدام بدون عنوان');
                if ($title === '') {
                    continue;
                }

                MeetingActionItem::query()->firstOrCreate([
                    'tenant_id' => $meeting->tenant_id,
                    'meeting_id' => $meeting->getKey(),
                    'title' => $title,
                ], [
                    'description' => $item['description'] ?? null,
                    'assignee_id' => $item['assignee_id'] ?? null,
                    'due_date' => $item['due_date'] ?? null,
                    'priority' => $item['priority'] ?? null,
                    'status' => 'open',
                ]);
            }

            MeetingAiRun::query()->create([
                'tenant_id' => $meeting->tenant_id,
                'meeting_id' => $meeting->getKey(),
                'action_type' => 'minutes',
                'provider' => $result->provider,
                'output_hash' => $this->hashResult($result),
                'created_by' => $actor?->getAuthIdentifier(),
            ]);

            return $minute;
        });

        $this->auditService->log('meetings.ai.minutes.generated', $meeting, [
            'minutes_id' => $minutes->getKey(),
        ], $actor);

        event(new MeetingAiMinutesGenerated(MeetingDto::fromModel($meeting), $payload));
        $this->followUpService->notifyMinutes($meeting, $minutes);

        return ['ok' => true, 'minutes' => $minutes, 'result' => $result];
    }

    /**
     * @return array{ok: bool, message?: string, summary?: string}
     */
    public function generateRecap(Meeting $meeting, ?Authenticatable $actor = null): array
    {
        $message = $this->canGenerateRecap($meeting);
        if ($message) {
            return ['ok' => false, 'message' => $message];
        }

        $meeting->loadMissing(['agendaItems', 'notes', 'transcripts']);
        $transcriptPayload = $this->transcriptService->buildTranscriptPayload($meeting);

        $text = (string) ($transcriptPayload['text'] ?? '');
        if ($text === '') {
            $text = (string) ($meeting->notes?->content_longtext ?: $this->renderAgendaText($meeting));
        }

        if ($text === '') {
            return ['ok' => false, 'message' => 'داده‌ای برای جمع‌بندی موجود نیست.'];
        }

        $schema = [
            'type' => 'object',
            'properties' => [
                'summary' => ['type' => 'string'],
            ],
        ];

        $result = $this->aiService->summarize('meetings', 'recap', $text, $schema, $this->buildMeetingContext($meeting), [], $actor);
        if (! $result->ok) {
            return ['ok' => false, 'message' => $result->error ?: 'درخواست ناموفق بود.'];
        }

        return ['ok' => true, 'summary' => (string) ($result->output_json['summary'] ?? $result->output_text ?? '')];
    }

    protected function buildMeetingContext(Meeting $meeting): array
    {
        $template = $this->resolveTemplate($meeting);

        return [
            'title' => $meeting->title,
            'scheduled_at' => $meeting->scheduled_at?->toIso8601String(),
            'duration_minutes' => $meeting->duration_minutes,
            'organizer' => $meeting->organizer?->name,
            'minutes_format' => $meeting->minutes_format,
            'attendees' => $meeting->attendees->map(fn ($attendee) => [
                'name' => $attendee->name ?: $attendee->user?->name,
                'role' => $attendee->role,
                'status' => $attendee->attendance_status,
            ])->toArray(),
            'agenda' => $meeting->agendaItems->map(fn ($item) => [
                'title' => $item->title,
                'description' => $item->description,
                'timebox_minutes' => $item->timebox_minutes,
            ])->toArray(),
            'notes' => $meeting->notes?->content_longtext,
            'template' => $template ? [
                'name' => $template->name,
                'format' => $template->format,
                'sections' => $template->sections_enabled_json,
                'custom_prompts' => $template->custom_prompts_json,
                'minutes_schema' => $template->minutes_schema_json,
            ] : null,
            'linked_workhub_items' => $meeting->actionItems
                ->whereNotNull('linked_workhub_item_id')
                ->map(fn ($item) => $item->linked_workhub_item_id)
                ->values()
                ->toArray(),
        ];
    }

    protected function resolveTemplate(Meeting $meeting): ?MeetingTemplate
    {
        $templateId = data_get($meeting->meta, 'template_id');
        if (! $templateId) {
            return null;
        }

        return MeetingTemplate::query()
            ->where('tenant_id', $meeting->tenant_id)
            ->whereKey($templateId)
            ->first();
    }

    /**
     * @return array<string, mixed> | null
     */
    protected function resolveMinutesSchema(Meeting $meeting): ?array
    {
        $template = $this->resolveTemplate($meeting);
        if (! $template) {
            return null;
        }

        return $template->minutes_schema_json;
    }

    protected function renderAgendaText(Meeting $meeting): string
    {
        $agenda = $meeting->agendaItems->map(fn ($item) => '- '.$item->title)->implode("\n");

        return $agenda;
    }

    protected function createManualMinutesDraft(
        Meeting $meeting,
        array $context,
        array $transcriptPayload,
        ?Authenticatable $actor = null,
    ): MeetingMinute {
        $summary = "### صورتجلسه دستی\n";
        $summary .= "\n**عنوان جلسه:** {$meeting->title}\n";
        if ($meeting->scheduled_at) {
            $summary .= "**زمان:** {$meeting->scheduled_at->format('Y-m-d H:i')}\n";
        }
        if ($meeting->agendaItems->isNotEmpty()) {
            $summary .= "\n**دستور جلسه:**\n".$this->renderAgendaText($meeting)."\n";
        }
        if (! empty($transcriptPayload['text'])) {
            $summary .= "\n**یادداشت‌ها:**\n".Str::limit((string) $transcriptPayload['text'], 800)."\n";
        }

        $minute = MeetingMinute::query()->create([
            'tenant_id' => $meeting->tenant_id,
            'meeting_id' => $meeting->getKey(),
            'overview_text' => 'پیش‌نویس دستی',
            'summary_markdown' => $summary,
            'decisions_json' => [],
            'risks_json' => [],
        ]);

        $this->auditService->log('meetings.minutes.draft.created', $meeting, [
            'minutes_id' => $minute->getKey(),
            'reason' => 'provider_failed',
        ], $actor);

        return $minute;
    }

    protected function hashResult(AiResult $result): ?string
    {
        $payload = $result->output_json ?? $result->output_text ?? null;
        if ($payload === null) {
            return null;
        }

        return hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    protected function dispatchJob(object $job): void
    {
        $pending = dispatch($job);

        $connection = config('filament-meetings.ai.queue.connection');
        if ($connection) {
            $pending->onConnection($connection);
        }

        $queue = config('filament-meetings.ai.queue.queue');
        if ($queue) {
            $pending->onQueue($queue);
        }
    }
}

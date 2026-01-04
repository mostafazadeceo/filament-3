<?php

namespace Haida\FilamentMeetings\Services;

use Filamat\IamSuite\Services\AuditService;
use Haida\FilamentAiCore\Services\AiPolicyService;
use Haida\FilamentAiCore\Services\RedactionService;
use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Models\MeetingTranscript;
use Haida\FilamentMeetings\Models\MeetingTranscriptSegment as TranscriptSegmentModel;
use Illuminate\Contracts\Auth\Authenticatable;

class MeetingTranscriptService
{
    public function __construct(
        protected MeetingConsentService $consentService,
        protected AiPolicyService $policyService,
        protected RedactionService $redactionService,
        protected AuditService $auditService,
    ) {}

    /**
     * @return array{ok: bool, transcript?: MeetingTranscript, message?: string}
     */
    public function storeTranscript(
        Meeting $meeting,
        string $content,
        string $language,
        string $source = 'manual',
        ?Authenticatable $actor = null,
    ): array {
        $content = trim($content);
        if ($content === '') {
            return ['ok' => false, 'message' => 'متن جلسه نمی‌تواند خالی باشد.'];
        }

        if ($this->consentService->isConsentRequired($meeting) && ! $this->consentService->isConsentSatisfied($meeting, $actor)) {
            return ['ok' => false, 'message' => 'ثبت متن جلسه بدون رضایت مجاز نیست.'];
        }

        $policy = $this->policyService->resolvePolicy($meeting->tenant);
        $redacted = $this->redactionService->redactInput(['text' => $content], (array) ($policy['redaction_policy'] ?? []));
        $safeContent = (string) ($redacted['text'] ?? $content);

        $allowStore = (bool) ($policy['allow_store_transcripts'] ?? false);

        $transcript = MeetingTranscript::query()->create([
            'tenant_id' => $meeting->tenant_id,
            'meeting_id' => $meeting->getKey(),
            'source' => $source,
            'language' => $language,
            'content_longtext' => $allowStore ? $safeContent : null,
        ]);

        if ($allowStore) {
            $segments = $this->segmentTranscript($safeContent);
            foreach ($segments as $segment) {
                TranscriptSegmentModel::query()->create(array_merge($segment, [
                    'tenant_id' => $meeting->tenant_id,
                    'meeting_id' => $meeting->getKey(),
                ]));
            }
        }

        $this->auditService->log('meetings.transcript.created', $meeting, [
            'transcript_id' => $transcript->getKey(),
            'source' => $source,
            'language' => $language,
            'stored' => $allowStore,
        ], $actor);

        return ['ok' => true, 'transcript' => $transcript];
    }

    /**
     * @return array<string, mixed>
     */
    public function buildTranscriptPayload(Meeting $meeting): array
    {
        $latest = MeetingTranscript::query()
            ->where('meeting_id', $meeting->getKey())
            ->latest('created_at')
            ->first();

        $segments = TranscriptSegmentModel::query()
            ->where('meeting_id', $meeting->getKey())
            ->orderBy('t_start_sec')
            ->orderBy('id')
            ->get()
            ->map(fn (TranscriptSegmentModel $segment) => [
                'id' => $segment->getKey(),
                't_start_sec' => $segment->t_start_sec,
                't_end_sec' => $segment->t_end_sec,
                'speaker_label' => $segment->speaker_label,
                'text' => $segment->text,
            ])
            ->toArray();

        return [
            'source' => $latest?->source,
            'language' => $latest?->language,
            'text' => $latest?->content_longtext,
            'segments' => $segments,
            'has_content' => (bool) ($latest?->content_longtext),
        ];
    }

    /**
     * @return array{ok: bool, message?: string}
     */
    public function requestProviderTranscription(Meeting $meeting, string $audioPath, ?Authenticatable $actor = null): array
    {
        if (! (bool) config('filament-meetings.transcripts.provider_enabled', false)) {
            return ['ok' => false, 'message' => 'ارائه‌دهنده تبدیل گفتار فعال نیست.'];
        }

        return ['ok' => false, 'message' => 'ارائه‌دهنده تبدیل گفتار پیکربندی نشده است.'];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function segmentTranscript(string $content): array
    {
        $lines = preg_split('/\r?\n/', $content) ?: [];
        $segments = [];
        $current = [
            'speaker_label' => null,
            'text' => '',
            't_start_sec' => null,
            't_end_sec' => null,
        ];

        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }

            [$timestamp, $rest] = $this->extractTimestamp($line);
            [$speaker, $text] = $this->extractSpeaker($rest);

            $isNewSegment = $speaker !== null || $timestamp !== null;
            if ($isNewSegment && trim($current['text']) !== '') {
                $segments[] = $current;
                $current = [
                    'speaker_label' => null,
                    'text' => '',
                    't_start_sec' => null,
                    't_end_sec' => null,
                ];
            }

            if ($speaker !== null) {
                $current['speaker_label'] = $speaker;
            }

            if ($timestamp !== null) {
                $current['t_start_sec'] = $timestamp;
            }

            $current['text'] = trim($current['text'].' '.$text);
        }

        if (trim($current['text']) !== '') {
            $segments[] = $current;
        }

        $count = count($segments);
        for ($i = 0; $i < $count - 1; $i++) {
            if ($segments[$i]['t_start_sec'] !== null && $segments[$i + 1]['t_start_sec'] !== null) {
                $segments[$i]['t_end_sec'] = max(0, (int) $segments[$i + 1]['t_start_sec']);
            }
        }

        return $segments;
    }

    /**
     * @return array{0: int|null, 1: string}
     */
    protected function extractTimestamp(string $line): array
    {
        if (! preg_match('/^(\[?(\d{1,2}:)?\d{1,2}:\d{2}\]?)\s*/', $line, $matches)) {
            return [null, $line];
        }

        $raw = trim($matches[1], '[]');
        $rest = trim(substr($line, strlen($matches[0])));

        $seconds = $this->timeToSeconds($raw);

        return [$seconds, $rest];
    }

    protected function timeToSeconds(string $time): ?int
    {
        $parts = explode(':', $time);
        if (count($parts) === 2) {
            [$minutes, $seconds] = $parts;

            return ((int) $minutes * 60) + (int) $seconds;
        }

        if (count($parts) === 3) {
            [$hours, $minutes, $seconds] = $parts;

            return ((int) $hours * 3600) + ((int) $minutes * 60) + (int) $seconds;
        }

        return null;
    }

    /**
     * @return array{0: string|null, 1: string}
     */
    protected function extractSpeaker(string $line): array
    {
        if (preg_match('/^([^:]{1,40}):\s*(.+)$/', $line, $matches)) {
            return [trim($matches[1]), trim($matches[2])];
        }

        return [null, $line];
    }
}

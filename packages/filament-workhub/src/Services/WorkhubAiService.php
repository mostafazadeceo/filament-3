<?php

namespace Haida\FilamentWorkhub\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAiCore\DataTransferObjects\AiResult;
use Haida\FilamentAiCore\Services\AiService;
use Haida\FilamentWorkhub\DTOs\ProjectDto;
use Haida\FilamentWorkhub\DTOs\WorkItemDto;
use Haida\FilamentWorkhub\Events\WorkhubAiFieldGenerated;
use Haida\FilamentWorkhub\Events\WorkhubAiProjectReportCreated;
use Haida\FilamentWorkhub\Events\WorkhubAiSubtasksCreated;
use Haida\FilamentWorkhub\Events\WorkhubAiSummaryCreated;
use Haida\FilamentWorkhub\Models\AiFieldRun;
use Haida\FilamentWorkhub\Models\AiSummary;
use Haida\FilamentWorkhub\Models\CustomField;
use Haida\FilamentWorkhub\Models\CustomFieldValue;
use Haida\FilamentWorkhub\Models\EntityReference;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\ProjectAiUpdate;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Support\WorkhubAiRateLimiter;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class WorkhubAiService
{
    public const PROMPT_VERSION = 'v1';

    public function __construct(
        protected AiService $aiService,
        protected WorkhubAiRateLimiter $rateLimiter,
        protected WorkItemCreator $workItemCreator,
    ) {}

    /**
     * @return array{result: AiResult, summary: AiSummary|null}
     */
    public function summarizeWorkItem(
        WorkItem $workItem,
        string $kind,
        string $storeType,
        array $options = [],
        ?Authenticatable $actor = null,
    ): array {
        $schema = $this->summarySchema();
        $text = $this->buildWorkItemText($workItem, $options);
        $context = $this->buildContext($workItem, $actor);

        $result = $this->aiService->summarize('workhub', $kind, $text, $schema, $context, [], $actor);
        if (! $result->ok) {
            return ['result' => $result, 'summary' => null];
        }

        $summaryJson = $this->formatSummaryJson($result, $kind);
        $summary = $this->storeSummary($workItem, $summaryJson, $storeType, $result, $actor, (int) ($options['ttl_minutes'] ?? 0));

        event(new WorkhubAiSummaryCreated(
            WorkItemDto::fromModel($workItem),
            $summary?->summary_json ?? $summaryJson,
            $storeType,
            $summary?->getKey()
        ));

        return ['result' => $result, 'summary' => $summary];
    }

    /**
     * @return array{result: AiResult, summary: AiSummary|null}
     */
    public function summarizeThread(
        WorkItem $workItem,
        string $storeType,
        array $options = [],
        ?Authenticatable $actor = null,
    ): array {
        $schema = $this->summarySchema();
        $text = $this->buildThreadText($workItem, $options);
        $context = $this->buildContext($workItem, $actor);

        $result = $this->aiService->summarize('workhub', 'thread_summary', $text, $schema, $context, [], $actor);
        if (! $result->ok) {
            return ['result' => $result, 'summary' => null];
        }

        $summaryJson = $this->formatSummaryJson($result, 'thread_summary');
        $summary = $this->storeSummary($workItem, $summaryJson, $storeType, $result, $actor, (int) ($options['ttl_minutes'] ?? 0));

        event(new WorkhubAiSummaryCreated(
            WorkItemDto::fromModel($workItem),
            $summary?->summary_json ?? $summaryJson,
            $storeType,
            $summary?->getKey()
        ));

        return ['result' => $result, 'summary' => $summary];
    }

    /**
     * @return array{result: AiResult, summary: AiSummary|null}
     */
    public function progressUpdate(
        WorkItem $workItem,
        int $windowDays,
        array $options = [],
        ?Authenticatable $actor = null,
    ): array {
        $schema = $this->summarySchema();
        $text = $this->buildProgressText($workItem, $windowDays);
        $context = $this->buildContext($workItem, $actor, ['window_days' => $windowDays]);

        $result = $this->aiService->summarize('workhub', 'progress_update', $text, $schema, $context, [], $actor);
        if (! $result->ok) {
            return ['result' => $result, 'summary' => null];
        }

        $summaryJson = $this->formatSummaryJson($result, 'progress_update');
        $summary = $this->storeSummary($workItem, $summaryJson, 'ttl', $result, $actor, (int) ($options['ttl_minutes'] ?? 0));

        event(new WorkhubAiSummaryCreated(
            WorkItemDto::fromModel($workItem),
            $summary?->summary_json ?? $summaryJson,
            'ttl',
            $summary?->getKey()
        ));

        return ['result' => $result, 'summary' => $summary];
    }

    /**
     * @return array{result: AiResult, suggestions: array<int, array<string, string>>}
     */
    public function suggestSubtasks(WorkItem $workItem, int $maxItems = 8, ?Authenticatable $actor = null): array
    {
        $text = $this->buildWorkItemText($workItem, ['include_comments' => true]);
        $schema = ['type' => 'array', 'items' => ['type' => 'string']];
        $context = $this->buildContext($workItem, $actor);

        $result = $this->aiService->extractActionItems('workhub', 'generate_subtasks', $text, $schema, $context, [], $actor);
        if (! $result->ok) {
            return ['result' => $result, 'suggestions' => []];
        }

        $suggestions = $this->normalizeActionItems($result, $maxItems);

        return ['result' => $result, 'suggestions' => $suggestions];
    }

    /**
     * @param  array<int, array<string, string>>  $items
     * @return array<int, WorkItem>
     */
    public function createSubtasks(WorkItem $workItem, array $items, ?Authenticatable $actor = null): array
    {
        if ($items === []) {
            return [];
        }

        $created = [];
        foreach ($items as $item) {
            $title = trim((string) ($item['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $data = [
                'tenant_id' => $workItem->tenant_id,
                'project_id' => $workItem->project_id,
                'title' => $title,
                'description' => $item['description'] ?? null,
                'priority' => $workItem->priority ?? 'medium',
                'reporter_id' => $actor?->getAuthIdentifier(),
                'created_by' => $actor?->getAuthIdentifier(),
                'updated_by' => $actor?->getAuthIdentifier(),
            ];

            $subtask = $this->workItemCreator->create($data);

            EntityReference::query()->create([
                'tenant_id' => $workItem->tenant_id,
                'source_type' => WorkItem::class,
                'source_id' => $workItem->getKey(),
                'target_type' => WorkItem::class,
                'target_id' => $subtask->getKey(),
                'relation_type' => 'subtask',
            ]);

            $created[] = $subtask;
        }

        if ($created !== []) {
            $payload = array_map(fn (WorkItem $item) => WorkItemDto::fromModel($item)->toArray(), $created);
            event(new WorkhubAiSubtasksCreated(WorkItemDto::fromModel($workItem), $payload));
        }

        return $created;
    }

    /**
     * @return Collection<int, WorkItem>
     */
    public function findSimilarTasks(WorkItem $workItem, int $limit = 5): Collection
    {
        $keywords = $this->extractKeywords($workItem->title.' '.$workItem->description);

        if ($keywords === []) {
            return collect();
        }

        return WorkItem::query()
            ->where('project_id', $workItem->project_id)
            ->whereKeyNot($workItem->getKey())
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('title', 'like', '%'.$keyword.'%')
                        ->orWhere('description', 'like', '%'.$keyword.'%');
                }
            })
            ->limit($limit)
            ->get();
    }

    /**
     * @return array{result: AiResult, report: ProjectAiUpdate|null}
     */
    public function generateExecutiveSummary(
        Project $project,
        array $filters = [],
        ?Authenticatable $actor = null,
    ): array {
        $schema = $this->executiveSummarySchema();
        $text = $this->buildProjectSummaryText($project, $filters);
        $context = $this->buildContext($project, $actor, $filters);

        $result = $this->aiService->summarize('workhub', 'executive_summary', $text, $schema, $context, [], $actor);
        if (! $result->ok) {
            return ['result' => $result, 'report' => null];
        }

        $reportJson = $this->formatSummaryJson($result, 'executive_summary');
        $markdown = $this->formatSummaryMarkdown($reportJson);
        $status = $this->deriveProjectStatus($project);

        $report = ProjectAiUpdate::query()->create([
            'tenant_id' => $project->tenant_id,
            'project_id' => $project->getKey(),
            'status_enum' => $status,
            'body_markdown' => $markdown,
            'created_by' => $actor?->getAuthIdentifier(),
        ]);

        event(new WorkhubAiProjectReportCreated(ProjectDto::fromModel($project), [
            'id' => $report->getKey(),
            'status' => $status,
            'body_markdown' => $markdown,
            'summary' => $reportJson,
        ]));

        return ['result' => $result, 'report' => $report];
    }

    /**
     * @return Collection<int, WorkItem>
     */
    public function stuckTasks(Project $project, int $days = 7): Collection
    {
        $threshold = now()->subDays($days);

        return WorkItem::query()
            ->where('project_id', $project->getKey())
            ->where('updated_at', '<', $threshold)
            ->whereHas('status', fn ($query) => $query->where('category', '!=', 'done'))
            ->limit(50)
            ->get();
    }

    /**
     * @return array{result: AiResult, run: AiFieldRun|null}
     */
    public function generateAiField(CustomField $field, WorkItem $workItem, ?Authenticatable $actor = null): array
    {
        $tenantId = (int) ($workItem->tenant_id ?? TenantContext::getTenantId());
        $rateLimit = (int) config('filament-workhub.ai.field.rate_limit_per_minute', 30);
        $this->rateLimiter->throttle('workhub-ai-field-'.$tenantId, $rateLimit);

        $prompt = $this->renderPrompt((string) data_get($field->settings, 'prompt_template', ''), $workItem);
        $schema = $this->parseSchema(data_get($field->settings, 'output_schema'));
        $context = $this->buildContext($workItem, $actor, ['field_id' => $field->getKey()]);

        $result = $this->aiService->summarize('workhub', 'ai_field', $prompt, $schema, $context, [], $actor);
        if (! $result->ok) {
            return ['result' => $result, 'run' => null];
        }

        $output = $result->output_json ?? ['text' => $result->output_text];

        $run = AiFieldRun::query()->create([
            'tenant_id' => $tenantId,
            'field_id' => $field->getKey(),
            'work_item_id' => $workItem->getKey(),
            'output_json' => is_array($output) ? $output : ['value' => $output],
            'created_by' => $actor?->getAuthIdentifier(),
        ]);

        CustomFieldValue::query()->updateOrCreate([
            'tenant_id' => $tenantId,
            'field_id' => $field->getKey(),
            'work_item_id' => $workItem->getKey(),
            'project_id' => $workItem->project_id,
        ], [
            'value' => [
                'value' => $output,
                'meta' => [
                    'provider' => $result->provider,
                    'model' => $result->model,
                    'run_id' => $run->getKey(),
                ],
            ],
        ]);

        event(new WorkhubAiFieldGenerated(WorkItemDto::fromModel($workItem), [
            'field' => [
                'id' => $field->getKey(),
                'name' => $field->name,
                'key' => $field->key,
            ],
            'output' => $output,
        ]));

        return ['result' => $result, 'run' => $run];
    }

    /**
     * @return array<string, mixed>
     */
    protected function summarySchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'خلاصه نکات' => ['type' => 'array', 'items' => ['type' => 'string']],
                'ریسک‌ها/گلوگاه‌ها' => ['type' => 'array', 'items' => ['type' => 'string']],
                'تصمیم‌ها/فرضیات' => ['type' => 'array', 'items' => ['type' => 'string']],
                'اقدامات پیشنهادی' => ['type' => 'array', 'items' => ['type' => 'string']],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function executiveSummarySchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'ابتکارات کلیدی' => ['type' => 'array', 'items' => ['type' => 'string']],
                'موارد عقب‌افتاده' => ['type' => 'array', 'items' => ['type' => 'string']],
                'ریسک‌ها' => ['type' => 'array', 'items' => ['type' => 'string']],
                'گام‌های بعدی' => ['type' => 'array', 'items' => ['type' => 'string']],
            ],
        ];
    }

    protected function buildWorkItemText(WorkItem $workItem, array $options = []): string
    {
        $includeComments = (bool) ($options['include_comments'] ?? true);
        $lines = [
            'عنوان: '.$workItem->title,
            'کلید: '.$workItem->key,
            'پروژه: '.($workItem->project?->name ?? ''),
            'وضعیت: '.($workItem->status?->name ?? ''),
            'اولویت: '.(string) $workItem->priority,
            'مسئول: '.($workItem->assignee?->name ?? ''),
            'توضیحات: '.($workItem->description ?? ''),
        ];

        if ($includeComments) {
            $lines[] = $this->formatComments($workItem, (int) ($options['comments_limit'] ?? 20));
        }

        return implode("\n", array_filter($lines));
    }

    protected function buildThreadText(WorkItem $workItem, array $options = []): string
    {
        return $this->formatComments($workItem, (int) ($options['comments_limit'] ?? 50));
    }

    protected function buildProgressText(WorkItem $workItem, int $windowDays): string
    {
        $lines = [
            'عنوان: '.$workItem->title,
            'کلید: '.$workItem->key,
            'بازه: '.$windowDays.' روز اخیر',
        ];

        $threshold = now()->subDays($windowDays);
        $comments = $workItem->comments()
            ->with('user')
            ->where('created_at', '>=', $threshold)
            ->latest()
            ->get();

        if ($comments->isNotEmpty()) {
            $lines[] = 'دیدگاه‌های اخیر:';
            foreach ($comments as $comment) {
                $lines[] = '- '.($comment->user?->name ?? 'کاربر').': '.trim((string) $comment->body);
            }
        }

        return implode("\n", array_filter($lines));
    }

    protected function buildProjectSummaryText(Project $project, array $filters = []): string
    {
        $query = WorkItem::query()
            ->where('project_id', $project->getKey())
            ->with(['status', 'assignee']);

        if (! empty($filters['status_id'])) {
            $query->where('status_id', (int) $filters['status_id']);
        }

        if (! empty($filters['updated_since_days'])) {
            $threshold = now()->subDays((int) $filters['updated_since_days']);
            $query->where('updated_at', '>=', $threshold);
        }

        $limit = (int) ($filters['limit'] ?? 40);
        $items = $query->limit($limit)->get();

        $lines = [
            'پروژه: '.$project->name,
            'کلید پروژه: '.$project->key,
            'وضعیت پروژه: '.($project->status ?? ''),
        ];

        $lines[] = 'آیتم‌های کلیدی:';
        foreach ($items as $item) {
            $lines[] = '- '.$item->key.' | '.$item->title.' | '.($item->status?->name ?? '').' | '.($item->assignee?->name ?? '');
        }

        return implode("\n", array_filter($lines));
    }

    protected function formatComments(WorkItem $workItem, int $limit): string
    {
        $comments = $workItem->comments()
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get();

        if ($comments->isEmpty()) {
            return 'دیدگاه‌ها: ندارد';
        }

        $lines = ['دیدگاه‌ها:'];
        foreach ($comments as $comment) {
            $lines[] = '- '.($comment->user?->name ?? 'کاربر').': '.trim((string) $comment->body);
        }

        return implode("\n", $lines);
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatSummaryJson(AiResult $result, string $kind): array
    {
        $sections = $this->normalizeSections($result->output_json ?? []);

        return [
            'kind' => $kind,
            'sections' => $sections,
            'text' => $result->output_text,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * @param  array<string, mixed>  $summaryJson
     */
    protected function formatSummaryMarkdown(array $summaryJson): string
    {
        $sections = (array) ($summaryJson['sections'] ?? []);
        if ($sections === []) {
            return (string) ($summaryJson['text'] ?? '');
        }

        $chunks = [];
        foreach ($sections as $title => $items) {
            $lines = array_map(fn ($item) => '- '.(string) $item, (array) $items);
            $chunks[] = '## '.$title."\n".implode("\n", $lines);
        }

        return implode("\n\n", $chunks);
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function normalizeSections(array $output): array
    {
        if ($output === []) {
            return [];
        }

        if (Arr::isList($output)) {
            return ['خلاصه نکات' => array_values(array_map('strval', $output))];
        }

        $sections = [];
        foreach ($output as $key => $value) {
            if (is_array($value)) {
                $sections[(string) $key] = array_values(array_map('strval', $value));
            } else {
                $sections[(string) $key] = [(string) $value];
            }
        }

        return $sections;
    }

    protected function storeSummary(
        WorkItem $workItem,
        array $summaryJson,
        string $storeType,
        AiResult $result,
        ?Authenticatable $actor,
        int $ttlMinutes,
    ): ?AiSummary {
        if ($storeType === '') {
            return null;
        }

        $ttlMinutes = $ttlMinutes > 0 ? $ttlMinutes : (int) config('filament-workhub.ai.summary_ttl_minutes', 60);
        $expiresAt = $storeType === 'ttl' ? now()->addMinutes($ttlMinutes) : null;

        return AiSummary::query()->create([
            'tenant_id' => $workItem->tenant_id,
            'work_item_id' => $workItem->getKey(),
            'created_by' => $actor?->getAuthIdentifier(),
            'type' => $storeType,
            'provider' => $result->provider,
            'prompt_version' => self::PROMPT_VERSION,
            'summary_json' => $summaryJson,
            'ttl_expires_at' => $expiresAt,
        ]);
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    protected function buildContext(object $record, ?Authenticatable $actor, array $extra = []): array
    {
        $base = [
            'tenant_id' => data_get($record, 'tenant_id'),
            'actor_id' => $actor?->getAuthIdentifier(),
        ];

        if ($record instanceof WorkItem) {
            $base['work_item_id'] = $record->getKey();
            $base['project_id'] = $record->project_id;
        }

        if ($record instanceof Project) {
            $base['project_id'] = $record->getKey();
        }

        return array_merge($base, $extra);
    }

    /**
     * @return array<int, array<string, string>>
     */
    protected function normalizeActionItems(AiResult $result, int $maxItems): array
    {
        $output = $result->output_json ?? [];
        if (Arr::isList($output)) {
            $items = array_map(fn ($item) => ['title' => (string) $item], $output);
        } else {
            $items = array_map(fn ($item) => ['title' => (string) $item], Arr::wrap(data_get($output, 'items', [])));
        }

        $items = array_filter($items, fn (array $item) => trim($item['title']) !== '');

        return array_slice($items, 0, $maxItems);
    }

    /**
     * @return array<int, string>
     */
    protected function extractKeywords(?string $text): array
    {
        $text = trim((string) $text);
        if ($text === '') {
            return [];
        }

        $tokens = preg_split('/\\s+/u', $text) ?: [];
        $tokens = array_filter($tokens, fn ($token) => mb_strlen($token) >= 3);
        $tokens = array_values(array_unique($tokens));

        return array_slice($tokens, 0, 6);
    }

    protected function deriveProjectStatus(Project $project): string
    {
        if ($project->status === 'archived') {
            return 'on_hold';
        }

        $items = WorkItem::query()->where('project_id', $project->getKey());
        $total = (int) $items->count();
        if ($total === 0) {
            return 'on_track';
        }

        $done = (int) $items->whereHas('status', fn ($query) => $query->where('category', 'done'))->count();
        if ($done === $total) {
            return 'complete';
        }

        $overdue = WorkItem::query()
            ->where('project_id', $project->getKey())
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::now()->toDateString())
            ->whereHas('status', fn ($query) => $query->where('category', '!=', 'done'))
            ->count();

        $stuck = $this->stuckTasks($project, (int) config('filament-workhub.ai.stuck_days', 7))->count();

        if ($overdue > 0 || $stuck > 0) {
            return 'at_risk';
        }

        return 'on_track';
    }

    protected function renderPrompt(string $template, WorkItem $workItem): string
    {
        if ($template === '') {
            return $this->buildWorkItemText($workItem);
        }

        $replacements = [
            '{{title}}' => $workItem->title,
            '{{description}}' => $workItem->description ?? '',
            '{{key}}' => $workItem->key,
            '{{project}}' => $workItem->project?->name ?? '',
            '{{status}}' => $workItem->status?->name ?? '',
            '{{priority}}' => $workItem->priority ?? '',
            '{{assignee}}' => $workItem->assignee?->name ?? '',
            '{{comments}}' => $this->formatComments($workItem, 20),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * @return array<string, mixed>
     */
    protected function parseSchema(mixed $schema): array
    {
        if (is_array($schema)) {
            return $schema;
        }

        if (is_string($schema) && trim($schema) !== '') {
            $decoded = json_decode($schema, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }
}

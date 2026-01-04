<?php

namespace Haida\FilamentWorkhub\Services;

use Haida\FilamentWorkhub\Models\AutomationRule;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Support\AutomationContext;
use Haida\FilamentWorkhub\Support\AutomationRegistry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WorkhubAutomationEngine
{
    public function __construct(protected AutomationRegistry $registry) {}

    public function handle(string $event, array $payload = []): void
    {
        $tenantId = (int) ($payload['tenant_id'] ?? data_get($payload, 'work_item.tenant_id') ?? data_get($payload, 'project.tenant_id') ?? 0);

        $rules = AutomationRule::query()
            ->where('is_active', true)
            ->where('trigger_type', $event)
            ->when($tenantId !== 0, fn ($query) => $query->where('tenant_id', $tenantId))
            ->get();

        foreach ($rules as $rule) {
            $this->applyRule($rule, $payload);
        }
    }

    public function runScheduled(?int $tenantId = null): void
    {
        $rules = AutomationRule::query()
            ->where('is_active', true)
            ->where('trigger_type', 'schedule')
            ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
            ->get();

        foreach ($rules as $rule) {
            if (! $this->isRuleDue($rule)) {
                continue;
            }

            $this->applyRule($rule, [
                'event' => 'schedule',
                'tenant_id' => $rule->tenant_id,
                'project_id' => $rule->project_id,
                'trigger' => $rule->trigger_config ?? [],
                'occurred_at' => now()->toIso8601String(),
            ]);

            $rule->forceFill(['last_ran_at' => now()])->save();
        }
    }

    protected function applyRule(AutomationRule $rule, array $payload): void
    {
        [$context, $workItem] = $this->buildContext($rule, $payload);

        if ($rule->project_id) {
            $eventProjectId = (int) ($context->get('work_item.project_id') ?? $context->get('project.id') ?? 0);
            if ($eventProjectId !== 0 && $eventProjectId !== (int) $rule->project_id) {
                return;
            }
        }

        if (! $this->passesConditions($rule, $context)) {
            return;
        }

        if ($workItem) {
            DB::transaction(fn () => $this->executeActions($rule, $context));

            return;
        }

        $this->executeActions($rule, $context);
    }

    protected function passesConditions(AutomationRule $rule, AutomationContext $context): bool
    {
        $conditions = $rule->conditions ?? [];
        if (! is_array($conditions) || $conditions === []) {
            return true;
        }

        foreach ($conditions as $condition) {
            $type = $condition['type'] ?? null;
            if (! $type) {
                continue;
            }

            $handler = $this->registry->getConditionHandler($type);
            if (! $handler) {
                continue;
            }

            $config = (array) ($condition['config'] ?? []);
            if (! $handler($context, $config)) {
                return false;
            }
        }

        return true;
    }

    protected function executeActions(AutomationRule $rule, AutomationContext $context): void
    {
        $actions = $rule->actions ?? [];
        if (! is_array($actions) || $actions === []) {
            return;
        }

        foreach ($actions as $action) {
            $type = $action['type'] ?? null;
            if (! $type) {
                continue;
            }

            $handler = $this->registry->getActionHandler($type);
            if (! $handler) {
                continue;
            }

            $config = (array) ($action['config'] ?? []);
            try {
                $handler($context, $config);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }
    }

    /**
     * @return array{0: AutomationContext, 1: WorkItem|null}
     */
    protected function buildContext(AutomationRule $rule, array $payload): array
    {
        $workItem = null;
        $project = null;

        $workItemId = data_get($payload, 'work_item.id') ?? data_get($payload, 'work_item_id');
        if ($workItemId) {
            $workItem = WorkItem::query()
                ->with(['labels', 'customFieldValues.field', 'project'])
                ->find($workItemId);

            $project = $workItem?->project;
        }

        if (! $project && $rule->project_id) {
            $project = Project::query()->find($rule->project_id);
        }

        if ($project && $rule->project_id && (int) $project->getKey() !== (int) $rule->project_id) {
            return [new AutomationContext($payload, null, $project), null];
        }

        return [new AutomationContext($payload, $workItem, $project), $workItem];
    }

    protected function isRuleDue(AutomationRule $rule): bool
    {
        $config = $rule->trigger_config ?? [];
        $intervalMinutes = isset($config['interval_minutes']) ? (int) $config['interval_minutes'] : null;

        if (! $intervalMinutes || $intervalMinutes <= 0) {
            return true;
        }

        $lastRanAt = $rule->last_ran_at;
        if (! $lastRanAt) {
            return true;
        }

        $last = $lastRanAt instanceof Carbon ? $lastRanAt : Carbon::parse($lastRanAt);

        return $last->diffInMinutes(now()) >= $intervalMinutes;
    }
}

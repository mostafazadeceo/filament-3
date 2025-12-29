<?php

namespace Haida\FilamentWorkhub\Support;

use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\WorkItem;

class AutomationContext
{
    /** @var array<string, mixed> */
    protected array $data;

    public function __construct(
        protected array $payload,
        protected ?WorkItem $workItem = null,
        protected ?Project $project = null,
    ) {
        $this->data = $this->buildData();
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function workItem(): ?WorkItem
    {
        return $this->workItem;
    }

    public function project(): ?Project
    {
        return $this->project;
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return $this->data;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->data, $key, $default);
    }

    public function resolveValue(mixed $value): mixed
    {
        if (is_string($value)) {
            return $this->resolveString($value);
        }

        if (is_array($value)) {
            return collect($value)
                ->map(fn ($item) => $this->resolveValue($item))
                ->toArray();
        }

        return $value;
    }

    protected function resolveString(string $value): string
    {
        return (string) preg_replace_callback('/\{\{\s*(.+?)\s*\}\}/', function (array $matches) {
            $key = $matches[1] ?? '';

            return (string) data_get($this->data, $key, '');
        }, $value);
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildData(): array
    {
        $data = $this->payload;
        $data['now'] = now()->toIso8601String();

        if ($this->project) {
            $data['project'] = array_merge((array) ($data['project'] ?? []), [
                'id' => $this->project->getKey(),
                'name' => $this->project->name,
                'key' => $this->project->key,
                'status' => $this->project->status,
            ]);
        }

        if ($this->workItem) {
            $data['work_item'] = array_merge((array) ($data['work_item'] ?? []), [
                'id' => $this->workItem->getKey(),
                'project_id' => $this->workItem->project_id,
                'status_id' => $this->workItem->status_id,
                'priority' => $this->workItem->priority,
                'assignee_id' => $this->workItem->assignee_id,
                'due_date' => optional($this->workItem->due_date)->toDateString(),
                'labels' => $this->workItem->labels->pluck('id')->all(),
                'custom_fields' => $this->customFieldsMap(),
            ]);
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    protected function customFieldsMap(): array
    {
        if (! $this->workItem) {
            return [];
        }

        return $this->workItem->customFieldValues
            ->mapWithKeys(function ($value) {
                $key = $value->field?->key;
                if (! $key) {
                    return [];
                }

                return [$key => $value->value];
            })
            ->toArray();
    }
}

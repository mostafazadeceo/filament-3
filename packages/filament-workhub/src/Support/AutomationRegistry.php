<?php

namespace Haida\FilamentWorkhub\Support;

use Closure;

class AutomationRegistry
{
    /** @var array<string, array{label: string, meta: array<string, mixed>}> */
    protected array $triggers = [];

    /** @var array<string, array{label: string, handler: Closure}> */
    protected array $conditions = [];

    /** @var array<string, array{label: string, handler: Closure}> */
    protected array $actions = [];

    protected bool $defaultsRegistered = false;

    public function registerTrigger(string $key, string $label, array $meta = []): void
    {
        $this->triggers[$key] = [
            'label' => $label,
            'meta' => $meta,
        ];
    }

    public function registerCondition(string $key, string $label, Closure $handler): void
    {
        $this->conditions[$key] = [
            'label' => $label,
            'handler' => $handler,
        ];
    }

    public function registerAction(string $key, string $label, Closure $handler): void
    {
        $this->actions[$key] = [
            'label' => $label,
            'handler' => $handler,
        ];
    }

    /**
     * @return array<string, array{label: string, meta: array<string, mixed>}>
     */
    public function triggers(): array
    {
        return $this->triggers;
    }

    /**
     * @return array<string, array{label: string, handler: Closure}>
     */
    public function conditions(): array
    {
        return $this->conditions;
    }

    /**
     * @return array<string, array{label: string, handler: Closure}>
     */
    public function actions(): array
    {
        return $this->actions;
    }

    /**
     * @return array<string, string>
     */
    public function triggerOptions(): array
    {
        return collect($this->triggers)
            ->mapWithKeys(fn (array $item, string $key) => [$key => $item['label']])
            ->toArray();
    }

    /**
     * @return array<string, string>
     */
    public function conditionOptions(): array
    {
        return collect($this->conditions)
            ->mapWithKeys(fn (array $item, string $key) => [$key => $item['label']])
            ->toArray();
    }

    /**
     * @return array<string, string>
     */
    public function actionOptions(): array
    {
        return collect($this->actions)
            ->mapWithKeys(fn (array $item, string $key) => [$key => $item['label']])
            ->toArray();
    }

    public function getConditionHandler(string $key): ?Closure
    {
        return $this->conditions[$key]['handler'] ?? null;
    }

    public function getActionHandler(string $key): ?Closure
    {
        return $this->actions[$key]['handler'] ?? null;
    }

    public function registerDefaults(callable $callback): void
    {
        if ($this->defaultsRegistered) {
            return;
        }

        $callback($this);
        $this->defaultsRegistered = true;
    }
}

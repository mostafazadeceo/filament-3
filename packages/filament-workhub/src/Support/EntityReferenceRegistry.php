<?php

namespace Haida\FilamentWorkhub\Support;

use Closure;

class EntityReferenceRegistry
{
    /**
     * @var array<string, array<string, mixed>>
     */
    protected array $items = [];

    /**
     * @param  Closure(object):string  $urlResolver
     * @param  Closure(object):string|null  $labelResolver
     */
    public function register(
        string $type,
        string $modelClass,
        string $label,
        string $icon,
        Closure $urlResolver,
        ?Closure $labelResolver = null
    ): void {
        $this->items[$type] = [
            'type' => $type,
            'model' => $modelClass,
            'label' => $label,
            'icon' => $icon,
            'url' => $urlResolver,
            'label_resolver' => $labelResolver,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return array_values($this->items);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function get(string $type): ?array
    {
        return $this->items[$type] ?? null;
    }

    public function resolveLabel(string $type, object $model): ?string
    {
        $definition = $this->get($type);
        if (! $definition) {
            return null;
        }

        $resolver = $definition['label_resolver'] ?? null;
        if ($resolver instanceof Closure) {
            return $resolver($model);
        }

        if (property_exists($model, 'name')) {
            return (string) $model->name;
        }

        if (method_exists($model, '__toString')) {
            return (string) $model;
        }

        return null;
    }
}

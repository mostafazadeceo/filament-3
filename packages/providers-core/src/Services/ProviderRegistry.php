<?php

declare(strict_types=1);

namespace Haida\ProvidersCore\Services;

use Haida\ProvidersCore\Contracts\ProviderAdapter;
use RuntimeException;

class ProviderRegistry
{
    /** @var array<string, class-string<ProviderAdapter>> */
    protected array $adapters = [];

    public function register(string $key, string $adapterClass): void
    {
        $this->adapters[$key] = $adapterClass;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->adapters);
    }

    /**
     * @return array<int, string>
     */
    public function keys(): array
    {
        return array_keys($this->adapters);
    }

    public function resolve(string $key): ProviderAdapter
    {
        $adapterClass = $this->adapters[$key] ?? null;
        if (! $adapterClass) {
            throw new RuntimeException("Provider adapter not registered: {$key}");
        }

        return app($adapterClass);
    }
}

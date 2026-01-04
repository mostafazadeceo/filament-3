<?php

namespace Haida\FilamentAiCore\Services;

use Haida\FilamentAiCore\Contracts\AiProviderInterface;
use Haida\FilamentAiCore\Providers\MockAiProvider;

class AiProviderManager
{
    public function resolve(?string $providerName = null): AiProviderInterface
    {
        $providerName = $providerName ?: (string) config('filament-ai-core.default_provider', 'mock');
        $map = (array) config('filament-ai-core.provider_map', []);

        $class = $map[$providerName] ?? MockAiProvider::class;

        return app($class);
    }

    public function isEnabled(string $providerName): bool
    {
        return (bool) config('filament-ai-core.providers.'.$providerName.'.enabled', false);
    }

    /**
     * @return array<int, string>
     */
    public function availableProviders(): array
    {
        $map = (array) config('filament-ai-core.provider_map', []);

        return array_keys($map);
    }
}

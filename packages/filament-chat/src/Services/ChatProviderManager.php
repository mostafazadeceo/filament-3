<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Services;

use Haida\FilamentChat\Contracts\ChatProviderInterface;
use Haida\FilamentChat\Models\ChatConnection;
use RuntimeException;

class ChatProviderManager
{
    public function resolve(ChatConnection $connection): ChatProviderInterface
    {
        $providers = (array) config('filament-chat.providers', []);
        $key = $connection->provider ?: config('filament-chat.default_provider', 'rocket_chat');
        $providerConfig = $providers[$key] ?? null;
        $class = $providerConfig['class'] ?? null;
        // allow per-tenant override (shared RC but different token/team if ever needed)
        if (isset($connection->settings['provider_class']) && class_exists($connection->settings['provider_class'])) {
            $class = $connection->settings['provider_class'];
        }

        if (! $class || ! class_exists($class)) {
            throw new RuntimeException('Chat provider not configured: '.$key);
        }

        return app($class);
    }
}

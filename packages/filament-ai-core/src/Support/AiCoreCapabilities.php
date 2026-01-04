<?php

namespace Haida\FilamentAiCore\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;

final class AiCoreCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-ai-core',
            [
                'ai.manage',
                'ai.audit.view',
            ],
            [
                'ai' => true,
            ],
            [],
            [],
            [
                'ai' => 'هوش مصنوعی',
                'ai_policies' => 'سیاست‌های هوش مصنوعی',
                'ai_audit' => 'لاگ هوش مصنوعی',
            ]
        );

        self::$registered = true;
    }
}

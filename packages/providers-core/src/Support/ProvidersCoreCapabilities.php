<?php

declare(strict_types=1);

namespace Haida\ProvidersCore\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\ProvidersCore\Policies\ProviderJobLogPolicy;

final class ProvidersCoreCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'providers-core',
            self::permissions(),
            [
                'provider_job_logs' => true,
            ],
            [],
            [
                ProviderJobLogPolicy::class,
            ],
            [
                'provider_job_logs' => 'لاگ‌های Provider',
            ]
        );

        self::$registered = true;
    }

    /**
     * @return array<int, string>
     */
    public static function permissions(): array
    {
        return [
            'provider.job_log.view',
            'provider.job_log.manage',
        ];
    }
}

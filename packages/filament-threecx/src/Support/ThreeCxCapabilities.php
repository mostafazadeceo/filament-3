<?php

namespace Haida\FilamentThreeCx\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentThreeCx\Policies\ThreeCxApiAuditLogPolicy;
use Haida\FilamentThreeCx\Policies\ThreeCxCallLogPolicy;
use Haida\FilamentThreeCx\Policies\ThreeCxContactPolicy;
use Haida\FilamentThreeCx\Policies\ThreeCxInstancePolicy;

final class ThreeCxCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'threecx',
            self::permissions(),
            [
                'threecx' => true,
            ],
            [],
            [
                ThreeCxInstancePolicy::class,
                ThreeCxCallLogPolicy::class,
                ThreeCxContactPolicy::class,
                ThreeCxApiAuditLogPolicy::class,
            ],
            [
                'threecx' => '3CX',
                'threecx_instances' => 'اتصال‌های 3CX',
                'threecx_call_logs' => 'تماس‌های 3CX',
                'threecx_contacts' => 'مخاطبین 3CX',
                'threecx_api_explorer' => 'کاوشگر API 3CX',
                'threecx_crm_connector' => 'اتصال CRM 3CX',
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
            'threecx.view',
            'threecx.manage',
            'threecx.sync',
            'threecx.api_explorer',
            'threecx.crm_connector',
        ];
    }
}

<?php

namespace Haida\TenancyDomains\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\TenancyDomains\Policies\SiteDomainPolicy;

final class TenancyDomainsCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'tenancy-domains',
            self::permissions(),
            [
                'site_domains' => true,
            ],
            [],
            [
                SiteDomainPolicy::class,
            ],
            [
                'site_domains' => 'دامنه‌ها',
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
            'site.domain.view',
            'site.domain.manage',
        ];
    }
}

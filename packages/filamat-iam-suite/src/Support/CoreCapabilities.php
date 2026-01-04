<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;

final class CoreCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filamat-iam-suite',
            CorePermissions::all(),
            [
                'wallet' => true,
                'subscriptions' => true,
                'notifications' => true,
                'api_docs' => true,
                'automation' => true,
                'access_requests' => true,
                'permission_snapshots' => true,
                'delegated_admin' => true,
                'pam' => true,
                'mfa' => true,
                'impersonation' => true,
                'sessions' => true,
                'sso' => false,
                'scim' => false,
            ],
            [
                'wallet.daily_limit' => null,
                'users.max' => null,
            ],
            [
                'UserPolicy',
                'TenantPolicy',
                'WalletPolicy',
            ],
            [
                'settings' => 'تنظیمات',
                'iam' => 'مدیریت دسترسی',
                'wallet' => 'کیف پول',
                'subscription' => 'اشتراک',
                'notifications' => 'اعلان‌ها',
                'automation' => 'اتوماسیون',
            ]
        );

        self::$registered = true;
    }
}

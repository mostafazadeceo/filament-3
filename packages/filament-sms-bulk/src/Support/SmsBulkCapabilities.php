<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\SmsBulk\Policies\SmsBulkModelPolicy;

final class SmsBulkCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-sms-bulk',
            self::permissions(),
            [
                'sms_bulk' => true,
            ],
            [],
            [
                SmsBulkModelPolicy::class,
            ],
            [
                'sms_bulk' => 'پیامک بالک',
                'sms_bulk_connections' => 'اتصالات پیامک',
                'sms_bulk_campaigns' => 'کمپین‌های پیامکی',
                'sms_bulk_reports' => 'گزارش‌های پیامکی',
                'sms_bulk_policies' => 'سیاست‌های ارسال',
                'sms_bulk_reseller' => 'نماینده/ریسلر',
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
            'sms-bulk.view',
            'sms-bulk.manage',
            'sms-bulk.connection.view',
            'sms-bulk.connection.manage',
            'sms-bulk.connection.test',
            'sms-bulk.sender.view',
            'sms-bulk.sender.manage',
            'sms-bulk.phonebook.view',
            'sms-bulk.phonebook.manage',
            'sms-bulk.phonebook.import',
            'sms-bulk.phonebook.export',
            'sms-bulk.pattern.view',
            'sms-bulk.pattern.manage',
            'sms-bulk.pattern.sync',
            'sms-bulk.draft.view',
            'sms-bulk.draft.manage',
            'sms-bulk.campaign.view',
            'sms-bulk.campaign.manage',
            'sms-bulk.campaign.submit',
            'sms-bulk.campaign.pause',
            'sms-bulk.campaign.resume',
            'sms-bulk.campaign.cancel',
            'sms-bulk.campaign.approve',
            'sms-bulk.campaign.override_suppression',
            'sms-bulk.report.view',
            'sms-bulk.report.export',
            'sms-bulk.report.sync',
            'sms-bulk.suppression.view',
            'sms-bulk.suppression.manage',
            'sms-bulk.policy.view',
            'sms-bulk.policy.manage',
            'sms-bulk.reseller.view',
            'sms-bulk.reseller.manage',
            'sms-bulk.ticket.manage',
            'sms-bulk.api.use',
        ];
    }
}

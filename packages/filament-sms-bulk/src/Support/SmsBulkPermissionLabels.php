<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Support;

final class SmsBulkPermissionLabels
{
    /**
     * @return array<string, array<string, string>>
     */
    public static function labels(): array
    {
        return [
            'fa' => [
                'sms-bulk.view' => 'مشاهده ماژول پیامک بالک',
                'sms-bulk.manage' => 'مدیریت کامل ماژول پیامک بالک',
                'sms-bulk.connection.manage' => 'مدیریت اتصال‌های پیامک',
                'sms-bulk.campaign.approve' => 'تایید کمپین پیامکی',
                'sms-bulk.campaign.override_suppression' => 'نادیده‌گرفتن سرکوب پیامک',
                'sms-bulk.reseller.manage' => 'مدیریت امکانات نمایندگی',
            ],
            'en' => [
                'sms-bulk.view' => 'View SMS Bulk module',
                'sms-bulk.manage' => 'Manage SMS Bulk module',
                'sms-bulk.connection.manage' => 'Manage SMS provider connections',
                'sms-bulk.campaign.approve' => 'Approve SMS campaigns',
                'sms-bulk.campaign.override_suppression' => 'Override suppression filters',
                'sms-bulk.reseller.manage' => 'Manage reseller capabilities',
            ],
            'ar' => [
                'sms-bulk.view' => 'عرض وحدة الرسائل بالجملة',
                'sms-bulk.manage' => 'إدارة وحدة الرسائل بالجملة',
                'sms-bulk.connection.manage' => 'إدارة اتصالات مزود الرسائل',
                'sms-bulk.campaign.approve' => 'اعتماد حملات الرسائل',
                'sms-bulk.campaign.override_suppression' => 'تجاوز قائمة الحظر',
                'sms-bulk.reseller.manage' => 'إدارة قدرات الوكيل',
            ],
        ];
    }
}

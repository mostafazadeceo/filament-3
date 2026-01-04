<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Support;

final class MailtrapLabels
{
    public static function connectionStatus(?string $status): string
    {
        return match ($status) {
            'active' => 'فعال',
            'inactive' => 'غیرفعال',
            default => $status ?: 'نامشخص',
        };
    }

    public static function inboxStatus(?string $status): string
    {
        return match ($status) {
            'active' => 'فعال',
            'inactive' => 'غیرفعال',
            default => $status ?: 'نامشخص',
        };
    }

    public static function domainStatus(?string $status): string
    {
        return match ($status) {
            'verified' => 'تایید شده',
            'pending' => 'در انتظار',
            'failed' => 'ناموفق',
            default => $status ?: 'نامشخص',
        };
    }

    public static function audienceStatus(?string $status): string
    {
        return match ($status) {
            'active' => 'فعال',
            'inactive' => 'غیرفعال',
            default => $status ?: 'نامشخص',
        };
    }

    public static function contactStatus(?string $status): string
    {
        return match ($status) {
            'subscribed' => 'فعال',
            'unsubscribed' => 'لغو عضویت',
            default => $status ?: 'نامشخص',
        };
    }

    public static function campaignStatus(?string $status): string
    {
        return match ($status) {
            'draft' => 'پیش‌نویس',
            'scheduled' => 'زمان‌بندی شده',
            'sending' => 'در حال ارسال',
            'sent' => 'ارسال شد',
            'failed' => 'ناموفق',
            default => $status ?: 'نامشخص',
        };
    }

    public static function sendStatus(?string $status): string
    {
        return match ($status) {
            'pending' => 'در صف',
            'sent' => 'ارسال شد',
            'failed' => 'ناموفق',
            default => $status ?: 'نامشخص',
        };
    }

    public static function boolean(bool $value): string
    {
        return $value ? 'بله' : 'خیر';
    }
}

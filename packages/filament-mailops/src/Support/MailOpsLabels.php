<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Support;

final class MailOpsLabels
{
    public static function status(?string $status): string
    {
        return match ($status) {
            'active' => 'فعال',
            'inactive' => 'غیرفعال',
            'pending' => 'در انتظار',
            'failed' => 'ناموفق',
            default => $status ?: 'نامشخص',
        };
    }

    public static function syncStatus(?string $status): string
    {
        return match ($status) {
            'pending' => 'در انتظار',
            'synced' => 'همگام',
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

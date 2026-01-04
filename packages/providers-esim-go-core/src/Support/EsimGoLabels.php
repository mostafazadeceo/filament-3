<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Support;

final class EsimGoLabels
{
    public static function connectionStatus(?string $status): string
    {
        return match ($status) {
            'active' => 'فعال',
            'inactive' => 'غیرفعال',
            default => $status ?: 'نامشخص',
        };
    }

    public static function orderStatus(?string $status): string
    {
        return match ($status) {
            'pending' => 'در انتظار',
            'validating' => 'در حال اعتبارسنجی',
            'processing' => 'در حال پردازش',
            'provisioning' => 'در حال تخصیص',
            'ready' => 'آماده تحویل',
            'delivered' => 'تحویل شده',
            'failed' => 'ناموفق',
            'cancelled' => 'لغوشده',
            default => $status ?: 'نامشخص',
        };
    }

    public static function boolean(?bool $value): string
    {
        return $value ? 'بله' : 'خیر';
    }
}

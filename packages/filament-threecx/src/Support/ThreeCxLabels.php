<?php

namespace Haida\FilamentThreeCx\Support;

class ThreeCxLabels
{
    public static function direction(?string $value): string
    {
        return match (strtolower((string) $value)) {
            'inbound' => 'ورودی',
            'outbound' => 'خروجی',
            'internal' => 'داخلی',
            'missed' => 'بی‌پاسخ',
            'chat' => 'گفتگو',
            default => $value ?: '-',
        };
    }

    public static function status(?string $value): string
    {
        return match (strtolower((string) $value)) {
            'answered' => 'پاسخ داده شده',
            'missed' => 'بی‌پاسخ',
            'failed' => 'ناموفق',
            'busy' => 'مشغول',
            'no_answer' => 'بدون پاسخ',
            'chat' => 'گفتگو',
            default => $value ?: '-',
        };
    }
}

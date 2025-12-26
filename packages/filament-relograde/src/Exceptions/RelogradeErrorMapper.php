<?php

namespace Haida\FilamentRelograde\Exceptions;

class RelogradeErrorMapper
{
    public static function messageForStatus(?int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'خطای اعتبارسنجی از رلوگرید.',
            401 => 'احراز هویت رلوگرید ناموفق بود.',
            402 => 'موجودی برای این عملیات کافی نیست.',
            403 => 'درخواست توسط رلوگرید رد شد.',
            404 => 'منبع مورد نظر در رلوگرید پیدا نشد.',
            429 => 'سقف نرخ رلوگرید رد شد. لطفا بعدا تلاش کنید.',
            500 => 'خطای سرور رلوگرید. لطفا بعدا تلاش کنید.',
            default => 'خطای غیرمنتظره از رلوگرید.',
        };
    }
}

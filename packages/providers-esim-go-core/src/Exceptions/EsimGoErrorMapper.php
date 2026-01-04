<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Exceptions;

final class EsimGoErrorMapper
{
    public static function messageForStatus(?int $status): string
    {
        return match ($status) {
            400 => 'درخواست نامعتبر برای eSIM Go ارسال شد.',
            401, 403 => 'احراز هویت eSIM Go نامعتبر است.',
            404 => 'منبع در eSIM Go پیدا نشد.',
            409 => 'درخواست eSIM Go در وضعیت تعارض است.',
            422 => 'پارامترهای ارسالی به eSIM Go معتبر نیستند.',
            429 => 'محدودیت نرخ درخواست‌های eSIM Go فعال شد.',
            500, 502, 503, 504 => 'سرویس eSIM Go موقتاً در دسترس نیست.',
            default => 'خطای ارتباط با eSIM Go رخ داد.',
        };
    }
}

<?php

namespace Haida\FilamentRelograde\Support;

class RelogradeLabels
{
    public static function environment(?string $value): string
    {
        return match ($value) {
            'sandbox' => 'آزمایشی',
            'production', 'live' => 'عملیاتی',
            default => (string) ($value ?? ''),
        };
    }

    public static function orderStatus(?string $value): string
    {
        return match ($value) {
            'created' => 'ایجاد شده',
            'pending' => 'در انتظار',
            'finished' => 'تکمیل شده',
            'cancelled' => 'لغو شده',
            'deleted' => 'حذف شده',
            default => (string) ($value ?? ''),
        };
    }

    public static function paymentStatus(?string $value): string
    {
        return match ($value) {
            'paid' => 'پرداخت‌شده',
            'unpaid' => 'پرداخت‌نشده',
            'pending' => 'در انتظار',
            'failed' => 'ناموفق',
            default => (string) ($value ?? ''),
        };
    }

    public static function processingStatus(?string $value): string
    {
        return match ($value) {
            'pending' => 'در انتظار',
            'processed' => 'پردازش شده',
            'failed' => 'ناموفق',
            default => (string) ($value ?? ''),
        };
    }

    public static function severity(?string $value): string
    {
        return match ($value) {
            'warning' => 'هشدار',
            'critical' => 'بحرانی',
            default => (string) ($value ?? ''),
        };
    }

    public static function alertType(?string $value): string
    {
        return match ($value) {
            'low_balance' => 'موجودی کم',
            default => (string) ($value ?? ''),
        };
    }

    public static function endpointName(?string $value): string
    {
        return match ($value) {
            'listBrands' => 'دریافت برندها',
            'listProducts' => 'دریافت محصولات',
            'listAccounts' => 'دریافت موجودی‌ها',
            'createOrder' => 'ایجاد سفارش',
            'confirmOrder' => 'تایید سفارش',
            'resolveOrder' => 'نهایی‌سازی سفارش',
            'cancelOrder' => 'لغو سفارش',
            'findOrder' => 'جستجوی سفارش',
            'listOrders' => 'دریافت سفارش‌ها',
            default => (string) ($value ?? ''),
        };
    }

    public static function webhookEvent(?string $value): string
    {
        return match ($value) {
            'ORDER_FINISHED' => 'تکمیل سفارش',
            default => (string) ($value ?? ''),
        };
    }

    public static function auditAction(?string $value): string
    {
        return match ($value) {
            'orders.create' => 'ایجاد سفارش',
            'orders.confirm' => 'تایید سفارش',
            'orders.resolve' => 'نهایی‌سازی سفارش',
            'orders.cancel' => 'لغو سفارش',
            'orders.refresh' => 'به‌روزرسانی سفارش',
            'brands.sync' => 'همگام‌سازی برندها',
            'brands.sync_failed' => 'خطا در همگام‌سازی برندها',
            'products.sync' => 'همگام‌سازی محصولات',
            'products.sync_failed' => 'خطا در همگام‌سازی محصولات',
            'accounts.sync' => 'همگام‌سازی موجودی‌ها',
            default => (string) ($value ?? ''),
        };
    }

    public static function entityType(?string $value): string
    {
        return match ($value) {
            'Haida\\FilamentRelograde\\Models\\RelogradeOrder' => 'سفارش',
            'Haida\\FilamentRelograde\\Models\\RelogradeProduct' => 'محصول',
            'Haida\\FilamentRelograde\\Models\\RelogradeBrand' => 'برند',
            'Haida\\FilamentRelograde\\Models\\RelogradeAccount' => 'موجودی',
            default => (string) ($value ?? ''),
        };
    }

    public static function boolean(?bool $value): string
    {
        return $value ? 'بله' : 'خیر';
    }
}

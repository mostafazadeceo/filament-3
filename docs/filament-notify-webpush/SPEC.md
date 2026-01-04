# SPEC — filament-notify-webpush

## معرفی
- پکیج: haida/filament-notify-webpush
- توضیح: Web push notifications channel for Filament Notify.
- Service Provider: Haida\FilamentNotify\WebPush\FilamentNotifyWebPushServiceProvider
- Filament Plugin: ندارد

## دامنه و قابلیت‌ها
- مدل‌ها:
- WebPushSubscription.php
- منابع Filament:
- ندارد
- کنترلرها/API:
- ندارد
- Jobs/Queue:
- ندارد
- Policyها:
- ندارد

## Tenancy و IAM
- BelongsToTenant در کد: [ASSUMPTION] نیازمند بررسی/افزودن
- TenantContext در کد: [ASSUMPTION] نیازمند بررسی
- IamAuthorization::allows در کد: [ASSUMPTION] نیازمند بررسی
- Capability Registry: [ASSUMPTION] در صورت وجود باید ثبت شود
- Scopeها (API): ندارد

## مدل داده
- Migrations:
- 2025_12_21_210000_create_fn_webpush_subscriptions_table.php
- جدول‌ها:
- fn_webpush_subscriptions
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: ندارد
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/filament-notify-webpush/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-notify-webpush/config/filament-notify-webpush.php
- کلیدهای env مرتبط:
- VAPID_SUBJECT

## استقرار در پنل‌ها
- Admin Panel: Plugin ندارد
- Tenant Panel: Plugin ندارد

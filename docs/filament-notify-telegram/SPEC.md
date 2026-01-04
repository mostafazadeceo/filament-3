# SPEC — filament-notify-telegram

## معرفی
- پکیج: haida/filament-notify-telegram
- توضیح: Telegram channel for Filament Notify.
- Service Provider: Haida\FilamentNotify\Telegram\FilamentNotifyTelegramServiceProvider
- Filament Plugin: ندارد

## دامنه و قابلیت‌ها
- مدل‌ها:
- ندارد
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
- ندارد
- جدول‌ها:
- ندارد
- ایندکس‌ها: [ASSUMPTION] نیازمند مرور مهاجرت‌ها

## API
- مسیر پایه: ندارد
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/filament-notify-telegram/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-notify-telegram/config/filament-notify-telegram.php
- کلیدهای env مرتبط:
- TELEGRAM_BASE_URL
- TELEGRAM_BOT_TOKEN
- TELEGRAM_DEFAULT_CHAT_ID

## استقرار در پنل‌ها
- Admin Panel: Plugin ندارد
- Tenant Panel: Plugin ندارد

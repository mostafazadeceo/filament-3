# SPEC — filament-notify-sms-ippanel

## معرفی
- پکیج: haida/filament-notify-sms-ippanel
- توضیح: IPPanel Pattern SMS channel for Filament Notify.
- Service Provider: Haida\FilamentNotify\SmsIppanel\FilamentNotifySmsIppanelServiceProvider
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
- جزئیات: `docs/filament-notify-sms-ippanel/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-notify-sms-ippanel/config/filament-notify-sms-ippanel.php
- کلیدهای env مرتبط:
- IPPANEL_API_KEY
- IPPANEL_BASE_URL
- IPPANEL_FROM_NUMBER

## استقرار در پنل‌ها
- Admin Panel: Plugin ندارد
- Tenant Panel: Plugin ندارد

# SPEC — filament-notify-whatsapp

## معرفی
- پکیج: haida/filament-notify-whatsapp
- توضیح: WhatsApp Cloud API channel for Filament Notify.
- Service Provider: Haida\FilamentNotify\WhatsApp\FilamentNotifyWhatsAppServiceProvider
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
- جزئیات: `docs/filament-notify-whatsapp/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-notify-whatsapp/config/filament-notify-whatsapp.php
- کلیدهای env مرتبط:
- WHATSAPP_BASE_URL
- WHATSAPP_PHONE_NUMBER_ID
- WHATSAPP_TOKEN

## استقرار در پنل‌ها
- Admin Panel: Plugin ندارد
- Tenant Panel: Plugin ندارد

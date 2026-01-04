# SPEC — providers-esim-go-webhooks

## معرفی
- پکیج: haida/providers-esim-go-webhooks
- توضیح: Webhook receiver for eSIM Go provider.
- Service Provider: Haida\ProvidersEsimGoWebhooks\ProvidersEsimGoWebhooksServiceProvider
- Filament Plugin: ندارد

## دامنه و قابلیت‌ها
- مدل‌ها:
- ندارد
- منابع Filament:
- ندارد
- کنترلرها/API:
- EsimGoWebhookController.php
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
- جزئیات: `docs/providers-esim-go-webhooks/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/providers-esim-go-webhooks/config/providers-esim-go-webhooks.php
- کلیدهای env مرتبط:
- ESIM_GO_WEBHOOK_RATE_LIMIT

## استقرار در پنل‌ها
- Admin Panel: Plugin ندارد
- Tenant Panel: Plugin ندارد

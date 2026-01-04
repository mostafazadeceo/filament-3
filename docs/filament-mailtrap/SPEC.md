# SPEC — filament-mailtrap

## معرفی
- پکیج: haida/filament-mailtrap
- توضیح: Filament UI for Mailtrap integration.
- Service Provider: Haida\FilamentMailtrap\FilamentMailtrapServiceProvider
- Filament Plugin: Haida\FilamentMailtrap\MailtrapPlugin (id: filament-mailtrap)

## دامنه و قابلیت‌ها
- مدل‌ها:
- ندارد
- منابع Filament:
- src/Resources/MailtrapAudienceResource.php
- src/Resources/MailtrapCampaignResource.php
- src/Resources/MailtrapConnectionResource.php
- src/Resources/MailtrapInboxResource.php
- src/Resources/MailtrapMessageResource.php
- src/Resources/MailtrapOfferResource.php
- src/Resources/MailtrapSendingDomainResource.php
- src/Resources/MailtrapSingleSendResource.php
- کنترلرها/API:
- ندارد
- Jobs/Queue:
- ندارد
- Policyها:
- ندارد

## Tenancy و IAM
- BelongsToTenant در کد: [ASSUMPTION] نیازمند بررسی/افزودن
- TenantContext در کد: [ASSUMPTION] نیازمند بررسی
- IamAuthorization::allows در کد: بله
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
- جزئیات: `docs/filament-mailtrap/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-mailtrap/config/filament-mailtrap.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider

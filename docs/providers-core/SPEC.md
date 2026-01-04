# SPEC — providers-core

## معرفی
- پکیج: haida/providers-core
- توضیح: Provider adapter contracts and job pipeline for external providers.
- Service Provider: Haida\ProvidersCore\ProvidersCoreServiceProvider
- Filament Plugin: Haida\ProvidersCore\ProvidersCorePlugin (id: providers-core)

## دامنه و قابلیت‌ها
- مدل‌ها:
- ProviderJobLog.php
- منابع Filament:
- src/Filament/Resources/ProviderJobLogResource.php
- کنترلرها/API:
- ندارد
- Jobs/Queue:
- ProviderActionJob.php
- Policyها:
- ProviderJobLogPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): ندارد

## مدل داده
- Migrations:
- 2025_12_30_000014_create_providers_core_job_logs_table.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: ندارد
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/providers-core/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/providers-core/config/providers-core.php
- کلیدهای env مرتبط:
- PROVIDERS_CORE_JOB_TIMEOUT
- PROVIDERS_CORE_QUEUE

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider

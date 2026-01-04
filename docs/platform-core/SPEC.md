# SPEC — platform-core

## معرفی
- پکیج: haida/platform-core
- توضیح: Platform core kernel for plugin registry and lifecycle.
- Service Provider: Haida\PlatformCore\PlatformCoreServiceProvider
- Filament Plugin: Haida\PlatformCore\PlatformCorePlugin (id: platform-core)

## دامنه و قابلیت‌ها
- مدل‌ها:
- PluginMigration.php
- PluginRegistry.php
- TenantPlugin.php
- منابع Filament:
- src/Filament/Resources/PluginRegistryResource.php
- src/Filament/Resources/TenantPluginResource.php
- کنترلرها/API:
- ندارد
- Jobs/Queue:
- ندارد
- Policyها:
- PluginRegistryPolicy.php
- TenantPluginPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: [ASSUMPTION] نیازمند بررسی/افزودن
- TenantContext در کد: [ASSUMPTION] نیازمند بررسی
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): ندارد

## مدل داده
- Migrations:
- 2025_12_30_000001_create_plugin_registry_tables.php
- 2025_12_30_000016_add_context_to_plugin_migrations.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: ندارد
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/platform-core/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/platform-core/config/platform-core.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت نشده در TenantPanelProvider

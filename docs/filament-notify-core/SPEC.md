# SPEC — filament-notify-core

## معرفی
- پکیج: haida/filament-notify-core
- توضیح: Core notification system for Filament v4 (rules, templates, triggers, logs).
- Service Provider: Haida\FilamentNotify\Core\FilamentNotifyServiceProvider
- Filament Plugin: Haida\FilamentNotify\Core\FilamentNotifyPlugin (id: filament-notify)

## دامنه و قابلیت‌ها
- مدل‌ها:
- ChannelSetting.php
- DeliveryLog.php
- NotificationRule.php
- Template.php
- Trigger.php
- منابع Filament:
- src/Resources/DeliveryLogResource.php
- src/Resources/NotificationRuleResource.php
- src/Resources/TemplateResource.php
- src/Resources/TriggerResource.php
- کنترلرها/API:
- ندارد
- Jobs/Queue:
- SendNotificationJob.php
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
- 2025_12_21_200000_create_fn_triggers_table.php
- 2025_12_21_200001_create_fn_templates_table.php
- 2025_12_21_200002_create_fn_notification_rules_table.php
- 2025_12_21_200003_create_fn_delivery_logs_table.php
- 2025_12_21_200004_create_fn_channel_settings_table.php
- جدول‌ها:
- fn_channel_settings
- fn_delivery_logs
- fn_notification_rules
- fn_templates
- fn_triggers
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: ندارد
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/filament-notify-core/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-notify-core/config/filament-notify.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت نشده در TenantPanelProvider

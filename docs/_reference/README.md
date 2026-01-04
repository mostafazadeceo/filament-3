# Reference Pack — Haida Filament Platform

این پوشه «منبع حقیقت» برای توسعه‌های بعدی است و با استانداردهای Filament v4، چند‌مستاجری و IAM هم‌راستاست. هر ماژول/پکیج باید این مسیر را مرجع قرار دهد.

## خلاصه اجرایی
این Reference Pack نمای جامع معماری، ماژول‌ها، APIها، امنیت و عملیات پلتفرم Haida را ارائه می‌دهد و با رویکرد package-first، Filament v4 و multi-tenant طراحی شده است. دو پنل Admin و Tenant با پلاگین‌های مجزا، هسته‌های IAM و Notification، و مجموعه گسترده‌ای از دامنه‌ها (Commerce، Workhub، Payments، Providers، CMS، Crypto و Operations) ستون‌های اصلی سیستم هستند. IAM Suite مبتنی بر `spatie/laravel-permission` با teams و `TenantContext`، مجوزها و scopeها را کنترل می‌کند و Capability Registry نقش محوری در یکپارچگی دسترسی‌ها دارد. APIهای نسخه‌بندی‌شده `/api/v1/*` همراه با middlewareهای `ApiKeyAuth`, `ApiAuth`, `ResolveTenant` و `filamat-iam.scope:*` ارائه می‌شوند و OpenAPI از طریق Filament API Docs Builder منتشر می‌شود.

از نظر داده، مهاجرت‌ها طیف گسترده‌ای از جداول را پوشش می‌دهند (IAM، Commerce، Workhub، Payments و سایر ماژول‌ها). ایندکس‌ها برای فیلترهای پرتکرار (tenant_id، status، updated_at و FKها) طراحی شده‌اند و در `MIGRATIONS/MIGRATION_GUIDE.md` جمع‌بندی شده‌اند. وبهوک‌ها با idempotency و امضای HMAC مدیریت می‌شوند و پردازش‌های سنگین از طریق صف انجام می‌گیرد. Runbook عملیاتی مسیر deploy/rollback و سناریوهای incident را استاندارد کرده است.

وضعیت QA: `php artisan test` و `deep_scenario_runner` با موفقیت اجرا شدند و Pint برای ماژول تغییرکرده (filament-crypto-gateway) سبز است. Lint سراسری عمداً اجرا نشد تا از تغییرات نامرتبط جلوگیری شود. نتیجه‌های کامل در `QA/TEST_REPORT.md` ثبت شده‌اند. فهرست فرضیات با اثر و پیشنهاد تصمیم در `ASSUMPTIONS_INDEX.md` آمده است و باید در چرخه‌های بعدی به مقدار قطعی تبدیل شود.

خلاصه اقدام‌های پیشنهادی: پاکسازی style در پکیج‌ها، قطعی‌سازی مفروضات (خصوصاً rate limitها و tenant scoping)، تکمیل payloadهای دقیق API بر اساس validationها، و تثبیت سیاست DLQ/Retry برای وبهوک‌ها. این سند به‌عنوان مرجع تصمیم‌گیری برای توسعه‌های بعدی استفاده شود.

## نقشه مستندات
- معماری کلان: `ARCHITECTURE/SYSTEM_OVERVIEW.md`
- Tenancy و IAM: `ARCHITECTURE/TENANCY_AND_IAM.md`
- امنیت: `ARCHITECTURE/SECURITY_MODEL.md`
- رویدادها و وبهوک‌ها: `ARCHITECTURE/EVENTING_AND_WEBHOOKS.md`
- مدل داده: `ARCHITECTURE/DATA_MODEL.md`
- Runbook عملیاتی: `ARCHITECTURE/OPERATIONS_RUNBOOK.md`
- برنامه تست و گزارش‌ها: `QA/TEST_PLAN.md`, `QA/SCENARIO_MATRIX.md`, `QA/TEST_REPORT.md`
- مهاجرت‌ها: `MIGRATIONS/MIGRATION_GUIDE.md`
- یکپارچه‌سازی‌ها: `INTEGRATIONS/API_INDEX.md`, `INTEGRATIONS/PERMISSIONS_CATALOG.md`, `INTEGRATIONS/CONFIG_KEYS.md`
- تصمیمات معماری (ADR): `DECISIONS/`
- تغییرات نسخه‌ها: `CHANGELOG.md` و `RELEASE_NOTES/`
- فهرست فرضیات: `ASSUMPTIONS_INDEX.md`

## لیست ماژول‌ها
| ماژول | SPEC | INSTALL | API |
| --- | --- | --- | --- |
| `blog` | `../blog/SPEC.md` | `../blog/INSTALL.md` | `../blog/API.md` |
| `commerce-catalog` | `../commerce-catalog/SPEC.md` | `../commerce-catalog/INSTALL.md` | `../commerce-catalog/API.md` |
| `commerce-checkout` | `../commerce-checkout/SPEC.md` | `../commerce-checkout/INSTALL.md` | `../commerce-checkout/API.md` |
| `commerce-orders` | `../commerce-orders/SPEC.md` | `../commerce-orders/INSTALL.md` | `../commerce-orders/API.md` |
| `content-cms` | `../content-cms/SPEC.md` | `../content-cms/INSTALL.md` | `../content-cms/API.md` |
| `feature-gates` | `../feature-gates/SPEC.md` | `../feature-gates/INSTALL.md` | `../feature-gates/API.md` |
| `filamat-iam-suite` | `../filamat-iam-suite/SPEC.md` | `../filamat-iam-suite/INSTALL.md` | `../filamat-iam-suite/API.md` |
| `filament-accounting-ir` | `../filament-accounting-ir/SPEC.md` | `../filament-accounting-ir/INSTALL.md` | `../filament-accounting-ir/API.md` |
| `filament-ai-core` | `../filament-ai-core/SPEC.md` | `../filament-ai-core/INSTALL.md` | `../filament-ai-core/API.md` |
| `filament-app-api` | `../filament-app-api/SPEC.md` | `../filament-app-api/INSTALL.md` | `../filament-app-api/API.md` |
| `filament-commerce-core` | `../filament-commerce-core/SPEC.md` | `../filament-commerce-core/INSTALL.md` | `../filament-commerce-core/API.md` |
| `filament-commerce-experience` | `../filament-commerce-experience/SPEC.md` | `../filament-commerce-experience/INSTALL.md` | `../filament-commerce-experience/API.md` |
| `filament-crypto-core` | `../filament-crypto-core/SPEC.md` | `../filament-crypto-core/INSTALL.md` | `../filament-crypto-core/API.md` |
| `filament-crypto-gateway` | `../filament-crypto-gateway/SPEC.md` | `../filament-crypto-gateway/INSTALL.md` | `../filament-crypto-gateway/API.md` |
| `filament-crypto-nodes` | `../filament-crypto-nodes/SPEC.md` | `../filament-crypto-nodes/INSTALL.md` | `../filament-crypto-nodes/API.md` |
| `filament-currency-rates` | `../filament-currency-rates/SPEC.md` | `../filament-currency-rates/INSTALL.md` | `../filament-currency-rates/API.md` |
| `filament-loyalty-club` | `../filament-loyalty-club/SPEC.md` | `../filament-loyalty-club/INSTALL.md` | `../filament-loyalty-club/API.md` |
| `filament-mailtrap` | `../filament-mailtrap/SPEC.md` | `../filament-mailtrap/INSTALL.md` | `../filament-mailtrap/API.md` |
| `filament-marketplace-connectors` | `../filament-marketplace-connectors/SPEC.md` | `../filament-marketplace-connectors/INSTALL.md` | `../filament-marketplace-connectors/API.md` |
| `filament-meetings` | `../filament-meetings/SPEC.md` | `../filament-meetings/INSTALL.md` | `../filament-meetings/API.md` |
| `filament-notify-bale` | `../filament-notify-bale/SPEC.md` | `../filament-notify-bale/INSTALL.md` | `../filament-notify-bale/API.md` |
| `filament-notify-core` | `../filament-notify-core/SPEC.md` | `../filament-notify-core/INSTALL.md` | `../filament-notify-core/API.md` |
| `filament-notify-mailtrap` | `../filament-notify-mailtrap/SPEC.md` | `../filament-notify-mailtrap/INSTALL.md` | `../filament-notify-mailtrap/API.md` |
| `filament-notify-sms-ippanel` | `../filament-notify-sms-ippanel/SPEC.md` | `../filament-notify-sms-ippanel/INSTALL.md` | `../filament-notify-sms-ippanel/API.md` |
| `filament-notify-telegram` | `../filament-notify-telegram/SPEC.md` | `../filament-notify-telegram/INSTALL.md` | `../filament-notify-telegram/API.md` |
| `filament-notify-webpush` | `../filament-notify-webpush/SPEC.md` | `../filament-notify-webpush/INSTALL.md` | `../filament-notify-webpush/API.md` |
| `filament-notify-whatsapp` | `../filament-notify-whatsapp/SPEC.md` | `../filament-notify-whatsapp/INSTALL.md` | `../filament-notify-whatsapp/API.md` |
| `filament-payments` | `../filament-payments/SPEC.md` | `../filament-payments/INSTALL.md` | `../filament-payments/API.md` |
| `filament-payroll-attendance` | `../filament-payroll-attendance/SPEC.md` | `../filament-payroll-attendance/INSTALL.md` | `../filament-payroll-attendance/API.md` |
| `filament-payroll-attendance-ir` | `../filament-payroll-attendance-ir/SPEC.md` | `../filament-payroll-attendance-ir/INSTALL.md` | `../filament-payroll-attendance-ir/API.md` |
| `filament-petty-cash-ir` | `../filament-petty-cash-ir/SPEC.md` | `../filament-petty-cash-ir/INSTALL.md` | `../filament-petty-cash-ir/API.md` |
| `filament-pos` | `../filament-pos/SPEC.md` | `../filament-pos/INSTALL.md` | `../filament-pos/API.md` |
| `filament-providers-esim-go` | `../filament-providers-esim-go/SPEC.md` | `../filament-providers-esim-go/INSTALL.md` | `../filament-providers-esim-go/API.md` |
| `filament-relograde` | `../filament-relograde/SPEC.md` | `../filament-relograde/INSTALL.md` | `../filament-relograde/API.md` |
| `filament-restaurant-ops` | `../filament-restaurant-ops/SPEC.md` | `../filament-restaurant-ops/INSTALL.md` | `../filament-restaurant-ops/API.md` |
| `filament-storefront-builder` | `../filament-storefront-builder/SPEC.md` | `../filament-storefront-builder/INSTALL.md` | `../filament-storefront-builder/API.md` |
| `filament-threecx` | `../filament-threecx/SPEC.md` | `../filament-threecx/INSTALL.md` | `../filament-threecx/API.md` |
| `filament-workhub` | `../filament-workhub/SPEC.md` | `../filament-workhub/INSTALL.md` | `../filament-workhub/API.md` |
| `mailtrap-core` | `../mailtrap-core/SPEC.md` | `../mailtrap-core/INSTALL.md` | `../mailtrap-core/API.md` |
| `observability` | `../observability/SPEC.md` | `../observability/INSTALL.md` | `../observability/API.md` |
| `page-builder` | `../page-builder/SPEC.md` | `../page-builder/INSTALL.md` | `../page-builder/API.md` |
| `payments-orchestrator` | `../payments-orchestrator/SPEC.md` | `../payments-orchestrator/INSTALL.md` | `../payments-orchestrator/API.md` |
| `platform-core` | `../platform-core/SPEC.md` | `../platform-core/INSTALL.md` | `../platform-core/API.md` |
| `providers-core` | `../providers-core/SPEC.md` | `../providers-core/INSTALL.md` | `../providers-core/API.md` |
| `providers-esim-go-commerce` | `../providers-esim-go-commerce/SPEC.md` | `../providers-esim-go-commerce/INSTALL.md` | `../providers-esim-go-commerce/API.md` |
| `providers-esim-go-core` | `../providers-esim-go-core/SPEC.md` | `../providers-esim-go-core/INSTALL.md` | `../providers-esim-go-core/API.md` |
| `providers-esim-go-webhooks` | `../providers-esim-go-webhooks/SPEC.md` | `../providers-esim-go-webhooks/INSTALL.md` | `../providers-esim-go-webhooks/API.md` |
| `site-builder-core` | `../site-builder-core/SPEC.md` | `../site-builder-core/INSTALL.md` | `../site-builder-core/API.md` |
| `tenancy-domains` | `../tenancy-domains/SPEC.md` | `../tenancy-domains/INSTALL.md` | `../tenancy-domains/API.md` |
| `theme-engine` | `../theme-engine/SPEC.md` | `../theme-engine/INSTALL.md` | `../theme-engine/API.md` |

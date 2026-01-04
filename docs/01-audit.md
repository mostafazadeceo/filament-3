# Repo Audit (PR-001)

## Versions & runtime
- PHP runtime: 8.4.15 (cli), composer requires `php ^8.2`.
- Laravel: v12.43.1 (`composer.lock`).
- Filament: v4.3.1 (`composer.lock`).
- Tailwind: v4.0.0, Vite: v7.0.7 (`package.json`).

## Panels (Filament)
- Admin panel: `App\Providers\Filament\AdminPanelProvider`.
- Tenant panel: `App\Providers\Filament\TenantPanelProvider`.
- Active plugins in admin panel:
  - `filamat/filamat-iam-suite`
  - `haida/filament-notify-*` (core + channels)
  - `haida/filament-currency-rates`
  - `haida/platform-core`
  - `haida/filament-relograde`
  - `haida/filament-workhub`
  - `haida/tenancy-domains`
  - `haida/content-cms`
  - `haida/blog`
  - `haida/commerce-catalog`
  - `haida/commerce-checkout`
  - `haida/commerce-orders`
  - `haida/payments-orchestrator`
  - `haida/page-builder`
  - `haida/site-builder-core`
  - `vendor/filament-accounting-ir`
  - `vendor/filament-payroll-attendance-ir`
  - `haida/filament-restaurant-ops`
  - `haida/filament-petty-cash-ir`
  - `zpmlabs/filament-api-docs-builder`
  - `diogogpinto/filament-auth-ui-enhancer` (conditional)
- Tenant panel plugins: Workhub, Site Builder Core, Page Builder, Content CMS, Blog, Commerce Catalog, Commerce Orders, Accounting IR, Payroll/Attendance IR, Restaurant Ops, Petty Cash, IAM suite, API docs builder.
- Tenant panel plugins includes Tenancy Domains for مدیریت دامنه‌ها.

## Tenancy model
- Tenant: `Filamat\IamSuite\Models\Tenant` (belongs to `Organization`, uses `tenant_user` pivot).
- Organization: `Filamat\IamSuite\Models\Organization`.
- Tenant resolution: `Filamat\IamSuite\Support\TenantContext`.
- Tenant scoping: `Filamat\IamSuite\Support\BelongsToTenant` + `TenantScope` global scope.
- Filament tenancy binding: `TenantPanelProvider->tenant(Tenant::class, 'slug', 'users')`.

## Auth / permissions
- Auth stack: Laravel Sanctum + session guards.
- RBAC: `spatie/laravel-permission` with teams enabled (tenant_id).
- IAM suite provides capability registry, access checks, and policy helpers.
- Subscriptions: `SubscriptionPlan` / `Subscription` with enforcement via `AccessService` (subscription gating enabled by default).

## Storage / queue / cache / session defaults
- Database connection default: sqlite (`config/database.php`), production evidence suggests MySQL (log entries).
- Queue default: `database` (`config/queue.php`).
- Cache store default: `database` (`config/cache.php`, `CACHE_STORE`).
- Session driver default: `database` (`config/session.php`).

## Existing modules/packages
- `filamat-iam-suite`: tenancy, RBAC, wallets, subscriptions, API middleware, audit primitives.
- `filament-notify-*`: notification core + channels (telegram, whatsapp, sms, bale, webpush).
- `filament-currency-rates`: FX rates + scheduler/job.
- `filament-relograde`: provider integration + webhooks + scheduler.
- `filament-workhub`: work tracking (workflow, kanban, automation).
- `filament-accounting-ir`: accounting ledger, journals, inventory, invoices, tax, e-invoice.
- `filament-payroll-attendance-ir`: HR, attendance, payroll.
- `filament-restaurant-ops`: procurement, inventory, cost control (linked to accounting inventory).
- `filament-petty-cash-ir`: petty cash with accounting hooks.
- `filament-payroll-attendance` (legacy/alternate package, not referenced in composer require).
- `zpmlabs/filament-api-docs-builder`: OpenAPI UI.
- `haida/platform-core`: plugin registry + lifecycle primitives (new).
- `haida/platform-core` UI: Filament resources for plugin registry + per-tenant enablement.
- `haida/feature-gates`: plan + tenant feature gating service (new).
- `haida/tenancy-domains`: site domain model + host resolution middleware (new).
- `haida/site-builder-core`: سایت ها، برندینگ، و تاریخچه انتشار (new).
- `haida/theme-engine`: رجیستری قالب ها و دارایی های relograde-v1 (new).
- `haida/page-builder`: صفحه ساز قالب محور با انتشار و sanitization (new).
- `haida/content-cms`: صفحات ثابت + سئو + سایت مپ (new).
- `haida/blog`: نوشته ها، دسته بندی ها و برچسب ها (new).
- `haida/commerce-catalog`: کاتالوگ فروش (محصول، واریانت، مدیا، مجموعه) (new).
- `haida/commerce-checkout`: سبد خرید و پرداخت کیف پول (new).
- `haida/commerce-orders`: سفارش‌ها و پرداخت‌ها (new).
- `haida/payments-orchestrator`: اتصال درگاه‌های tenant + وبهوک‌های امن (new).
- `haida/providers-core`: قراردادها و رجیستری Providerها + لاگ اجرای کارها (new).
- `haida/observability`: Correlation ID و لاگ context (new).
- Jalali/Hijri packages present: `ariaieboy/filament-jalali`, `geniusts/hijri-dates`, `mohamedsabil83/filament-hijri-picker`.

## Public routing & domain logic
- `routes/web.php` only returns `welcome` view (core app).
- Content CMS and Blog packages register public routes under middleware `resolve.site`:
  - صفحات: `/` و `/{slug}`
  - وبلاگ: `/blog` و `/blog/{slug}`
  - `sitemap.xml`
- Tenancy Domains package adds `SiteDomain` model and `resolve.site` middleware alias for host-based tenant/site resolution.
- Relograde exposes a webhook endpoint under its own package routes.

## Observed integration points
- Restaurant Ops models link to Accounting inventory (`accounting_inventory_item_id`, `accounting_inventory_warehouse_id`).
- Petty Cash uses accounting cash/bank accounts.
- IAM suite exposes wallet APIs and idempotent wallet transactions.
- Checkout برای محصولات track_inventory، سند انبار نوع issue ثبت می‌کند و موجودی را کاهش می‌دهد.
- Payments orchestrator شامل آداپتور HMAC برای اتصال به درگاه‌های tenant است.
- UI برای لاگ‌های Provider و بازپردازش dead-letter اضافه شده است.

## Gaps for new Site OS
- مسیرهای عمومی فقط برای صفحات و وبلاگ در حد پایه موجود است؛ رندر تم و page builder هنوز حداقلی است.
- Domain verification + allowlist + hook صدور TLS + TrustedHosts/TrustedProxies در bootstrap پیاده شده‌اند؛ Provider واقعی TLS هنوز نیازمند تنظیمات محیطی است.
- Feature gates برای CMS/Blog/Commerce (سفارش و پرداخت) متصل شده‌اند؛ Providerها هنوز توسعه نشده است.
- کاتالوگ فروش از مجوزهای IAM استفاده می‌کند و به جدول نرخ ارز برای تبدیل‌ها متصل است.

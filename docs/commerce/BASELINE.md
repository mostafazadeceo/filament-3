# خط مبنا (Baseline) اکوسیستم تجارت

## خلاصه سریع
این مخزن از قبل چند پکیج مرتبط با تجارت، پرداخت، سایت‌ساز و IAM دارد. این سند فقط آنچه موجود است را فهرست می‌کند تا تصمیم‌های معماری ما روی واقعیت استوار باشد.

## ماژول‌های موجود مرتبط با تجارت
- `packages/commerce-catalog`
  - مدل‌ها: `CatalogProduct`, `CatalogVariant`, `CatalogMedia`, `CatalogCollection`
  - مسیرها: `packages/commerce-catalog/routes/api.php` (پایه `/api/v1/commerce-catalog`)
  - منابع Filament: `packages/commerce-catalog/src/Filament/Resources/*`
  - سیاست‌ها و قابلیت‌ها: `packages/commerce-catalog/src/Policies/*`, `packages/commerce-catalog/src/Support/CatalogCapabilities.php`
  - اتصال اختیاری به حسابداری/انبار: `CatalogProduct` به `Vendor\FilamentAccountingIr\Models\ProductService` و `InventoryItem` متصل می‌شود.

- `packages/commerce-checkout`
  - مدل‌ها: `Cart`, `CartItem`
  - سرویس‌ها: `CartService`, `CheckoutService`, `OrderInventoryService`
  - مسیرها: `packages/commerce-checkout/routes/api.php` (پایه `/api/v1/commerce-checkout`)
  - منابع Filament: `packages/commerce-checkout/src/Filament/Resources/*` (ولی پلاگین Filament مستقل ندارد)

- `packages/commerce-orders`
  - مدل‌ها: `Order`, `OrderItem`, `OrderPayment`
  - اتصال به کیف پول IAM: `OrderPayment` دارای `wallet_transaction_id` و `wallet_hold_id` است.
  - مسیرها: `packages/commerce-orders/routes/api.php` (پایه `/api/v1/commerce-orders`)
  - پلاگین Filament: `packages/commerce-orders/src/CommerceOrdersPlugin.php`

## پرداخت‌ها و ارکستریشن
- `packages/payments-orchestrator`
  - مدل‌ها: `PaymentIntent`, `PaymentGatewayConnection`, `PaymentWebhookEvent`
  - سرویس‌ها: `GatewayRegistry`, `PaymentIntentService`, `WebhookHandler`
  - مسیرها: `packages/payments-orchestrator/routes/api.php` (پایه `/api/v1/payments-orchestrator`)
  - الگوی OpenAPI: `packages/payments-orchestrator/src/Support/PaymentsOpenApi.php`

## سایت‌ساز/Storefront و محتوا
- `packages/site-builder-core`
  - مدل‌ها: `Site` و قابلیت‌های دسترسی: `SiteBuilderCapabilities`
- `packages/page-builder`
  - مدل‌ها: `PageTemplate` و منابع Filament برای مدیریت قالب‌ها
- `packages/content-cms`
  - مدل‌ها و سرویس‌ها برای محتوای ساختارمند
- `packages/theme-engine`
  - `ThemeRegistry` و `ThemeDefinition` برای تم‌ها
- `packages/tenancy-domains`
  - مدیریت دامنه‌های سایت: `SiteDomain` و سرویس‌ها

## ماژول‌های مرتبط با سفارش/فاکتور/انبار (غیر تجارت مستقیم)
- `packages/filament-accounting-ir`
  - مدل‌های فاکتور فروش/خرید، انبار و اسناد انبار: `SalesInvoice`, `PurchaseInvoice`, `InventoryDoc`, `InventoryItem` و ...
- `packages/filament-restaurant-ops`
  - عملیات رستوران (اقلام، منو، فروش منو، خرید و انبار رستوران)
  - تاکید: این ماژول POS عمومی نیست و فقط از طریق آداپترها باید به POS تجارت متصل شود.

## وفاداری/کیف پول/پلن‌ها
- `packages/filamat-iam-suite`
  - کیف پول و تراکنش‌ها: `Wallet`, `WalletTransaction`, `WalletHold`
  - اشتراک و پلن: `Subscription`, `SubscriptionPlan`
  - Middlewareهای API و ResolveTenant: `Filamat\IamSuite\Http\Middleware\*`
- `packages/filament-loyalty-club`
  - آداپتر کیف پول: `WalletAdapterInterface` با امکان اتصال به IAM
- `packages/feature-gates`
  - ارزیابی دسترسی بر اساس پلن/اشتراک

## الگوهای معماری فعلی
- بسته‌ها با `spatie/laravel-package-tools` تعریف می‌شوند و مسیر/مهاجرت/کانفیگ/ترجمه ثبت می‌شود.
- پلاگین‌های Filament با `getId()`, `register()`, `boot()` در پکیج‌ها وجود دارند؛ ثبت در پنل‌ها دستی است.
  - ثبت فعلی پلاگین‌ها در `app/Providers/Filament/AdminPanelProvider.php`
  - ثبت فعلی پلاگین‌ها در `app/Providers/Filament/TenantPanelProvider.php`
- احراز مجوزها:
  - Policies با `IamAuthorization::allows()` و `IamAuthorization::resolveTenantFromRecord()`
  - Resources از `Filamat\IamSuite\Filament\Resources\IamResource` استفاده می‌کنند.
- اسکوب tenancy:
  - مدل‌ها غالباً `Filamat\IamSuite\Support\BelongsToTenant` دارند.
  - UI با `InteractsWithTenant` و سرویس‌ها با `TenantContext` کار می‌کنند.

## الگوی API و OpenAPI
- پایه API ماژول‌ها: `/api/v1/<module>`
- Middleware متداول: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, `filamat-iam.scope:<perm>`, `throttle`.
- OpenAPI به صورت آرایه در `Support/*OpenApi.php` و مسیر `openapi` ارائه می‌شود.

## سناریو رانر و تست‌های سناریویی
- `scripts/deep_scenario_runner.php`
  - نمونه‌های استفاده از Commerce (Catalog + Checkout) و PaymentsOrchestrator.
  - الگوی idempotent بودن: پاک‌سازی دیتابیس SQLite در شروع و اجرای seed/migrate.

## نکات کلیدی برای ادامه
- تجارت قبلی (catalog/checkout/orders) وجود دارد اما یکپارچه و کامل برای POS/Storefront Builder/Experience نیست.
- پرداخت‌ها یک اورکستریتور دارند اما به الزامات ایران/خارج و پرداخت دستی نیاز به توسعه دارد.
- زیرساخت IAM/Wallet/Subscription آماده است و باید به عنوان هسته مجوز/پرداخت داخلی استفاده شود.

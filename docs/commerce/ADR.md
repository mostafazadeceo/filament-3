# ADR — مرزبندی ماژول‌ها و هسته مشترک تجارت

## زمینه
- مخزن شامل پکیج‌های `commerce-*` (کاتالوگ/چک‌اوت/سفارش)، `payments-orchestrator`، و ابزارهای سایت‌ساز است.
- IAM و tenancy از طریق `filamat-iam-suite` پیاده‌سازی شده و باید مبنا قرار گیرد.
- نیاز جدید، ایجاد اکوسیستم کامل تجارت + POS + Storefront Builder + Payments + Experience + Marketplace است.

## تصمیم‌ها
1) **ایجاد پکیج‌های جدید مطابق مأموریت**
   - ایجاد پکیج‌های جدید با نام‌های زیر (مطابق الزام پروژه):
     - `packages/filament-commerce-core`
     - `packages/filament-payments`
     - `packages/filament-pos`
     - `packages/filament-storefront-builder`
     - `packages/filament-commerce-experience`
     - `packages/filament-marketplace-connectors`
   - دلیل: یکپارچگی نام‌گذاری، قابلیت توسعه، و جداسازی واضح دامنه‌ها.

2) **هم‌زیستی/مهاجرت تدریجی از پکیج‌های موجود**
   - `commerce-catalog`, `commerce-checkout`, `commerce-orders` به عنوان منبع فعلی داده و UI در نظر گرفته می‌شوند.
   - `filament-commerce-core` به‌صورت تدریجی API/مدل‌های هسته را همگرا می‌کند و لایه آداپتر برای سازگاری با پکیج‌های قبلی خواهد داشت.
   - دلیل: جلوگیری از شکست ناگهانی در UI/API و حفظ پایداری در محیط چند-مشتری.

3) **پرداخت‌ها: استفاده از الگوی موجود + توسعه در پکیج جدید**
   - `payments-orchestrator` الگوی PaymentIntent و Webhook دارد؛ در فاز اول به‌عنوان زیرساخت پایه استفاده می‌شود.
   - `filament-payments` API یکپارچه و Providerهای جدید (ایران/بین‌الملل/دستی) را ارائه می‌دهد و به مرور به جایگاه اصلی می‌رسد.
   - دلیل: استفاده از دارایی موجود، کاهش ریسک مهاجرت.

4) **Storefront Builder روی دارایی‌های فعلی**
   - `filament-storefront-builder` از `site-builder-core`, `page-builder`, `content-cms`, `theme-engine` استفاده می‌کند و بلاک‌ها/تم‌های فروشگاهی اضافه می‌کند.
   - دلیل: جلوگیری از دوباره‌سازی ابزارهای مدیریت سایت و تمرکز بر UX فروشگاهی.

5) **POS مستقل با آداپتر به RestaurantOps**
   - `filament-pos` مستقل از `filament-restaurant-ops` است و فقط از طریق آداپترها و مپینگ داده یکپارچه می‌شود.
   - دلیل: RestaurantOps برای POS عمومی طراحی نشده و محدودیت‌های دامنه دارد.

6) **هسته مشترک دامنه (Shared Kernel)**
   - در `filament-commerce-core`: تعریف آبجکت‌های پایه (Money, Address, Tax/VAT, Idempotency, AuditEvent, InventoryAdjustment) و قراردادهای مشترک.
   - دلیل: جلوگیری از دوباره‌سازی مفاهیم پایه بین پکیج‌ها.

7) **IAM و مجوزها**
   - مجوزها در `CapabilityRegistry` ثبت می‌شود و در UI و API اعمال می‌گردد.
   - مدل‌ها از `BelongsToTenant` و سرویس‌ها از `TenantContext` استفاده می‌کنند.
   - دلیل: رعایت الزام multi-tenant و امنیت.

8) **تصمیم‌های متاثر از BENCHMARK**
   - POS آفلاین + Outbox + Idempotency به‌صورت پروتکل رسمی (الهام از POSهای بازار مثل Square/Shopify POS).
   - Payment Intent + Webhook signature/replay protection به‌عنوان هسته پرداخت (الهام از Stripe).
   - Headless Storefront با API عمومی و بلاک‌های قابل نسخه‌بندی (الگوی Shopify/WooCommerce/Magento).
   - Connectorها با Rate limit و backoff (الگوی Amazon SP-API و eBay).
   - مدیریت نقدی صندوق و تسویه شیفت به‌صورت صریح (الگوی POSهای خرده‌فروشی).

9) **الگوی API ثابت**
   - `/api/v1/<module>` + `ApiKeyAuth` + `ApiAuth` + `ResolveTenant` + `filamat-iam.scope:<perm>` + `throttle`.
   - OpenAPI با `zpmlabs/filament-api-docs-builder` (الگوی `Support/*OpenApi.php`).
   - دلیل: سازگاری با الگوی فعلی پروژه.

10) **اعلان‌ها فقط از notify-core**
   - همه اعلان‌ها از `haida/filament-notify-core` و TriggerDispatcher.
   - دلیل: سیاست پروژه و جلوگیری از سیستم‌های موازی.

## پیامدها و نکات اجرایی
- مسیر مهاجرت از پکیج‌های `commerce-*` باید مستند شود و تا پایان انتقال، لایه سازگاری حفظ گردد.
- نام جدول‌ها باید دارای پیشوند هر پکیج باشد (`commerce_`, `payments_`, `pos_`, `store_`, `exp_`, `mkt_`).
- این ADR با یافته‌های BENCHMARK همگام‌سازی شده است.

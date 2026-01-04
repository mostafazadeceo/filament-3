# Architecture Overview

## Contexts
- **TenantContext**: از IAM Suite برای محدودسازی داده‌ها و دسترسی‌ها در تمام ماژول‌ها.
- **SiteContext**: در `tenancy-domains` برای نگاشت host → site → tenant استفاده می‌شود.

## Package Boundaries
- **platform-core**: رجیستری پلاگین‌ها و lifecycle (install/enable/upgrade/rollback).
  - شامل UI مدیریتی برای رجیستری افزونه‌ها و فعال‌سازی per-tenant.
  - ثبت context مهاجرت‌ها با `correlation_id` و `triggered_by_user_id`.
- **feature-gates**: فعال‌سازی ویژگی‌ها بر اساس پلن و override تننت.
- **tenancy-domains**: دامنه‌ها، middleware تشخیص سایت/تننت، و hook صدور TLS.
- **site-builder-core**: مدیریت سایت‌ها، برندینگ و تاریخچه انتشار.
- **theme-engine**: رجیستری قالب‌ها و asset pipeline.
- **page-builder**: قالب JSON و sanitization محتوا.
- **content-cms**: صفحات ثابت و سئو.
- **blog**: وبلاگ و زمان‌بندی انتشار.
- **commerce-catalog**: محصولات، واریانت‌ها و قیمت‌گذاری چندارزی.
- **commerce-checkout**: سبد خرید، پرداخت کیف پول، و صدور سند انبار Issue.
- **commerce-orders**: سفارش‌ها و پرداخت‌ها.
- **payments-orchestrator**: اتصال درگاه‌ها و وب‌هوک‌های امن (HMAC adapter).
- **providers-core**: قراردادها و رجیستری Providerها + لاگ اجرای کارها + UI بازپردازش.
- **filament-relograde**: Provider مرجع (Relograde) با آداپتر جدید.
- **providers-esim-go-core**: کلاینت و دامنه eSIM Go + مدل‌ها و سرویس‌ها.
- **providers-esim-go-commerce**: اتصال eSIM Go به commerce (catalog/checkout/orders).
- **providers-esim-go-webhooks**: دریافت callback + HMAC + نرمال‌سازی رخدادها.
- **filament-providers-esim-go**: UI مدیریت اتصال/کاتالوگ/سفارش‌ها/تحویل‌ها.
- **observability**: Correlation ID و context لاگ‌ها.

## Security & Tenancy
- تمام مدل‌های جدید از `BelongsToTenant` و `TenantScope` استفاده می‌کنند.
- وب‌هوک‌ها با امضا + idempotency کنترل می‌شوند.
- پرداخت‌ها فقط با token/intent نگهداری می‌شوند؛ اطلاعات کارت ذخیره نمی‌شود.

## Event & Workflow Highlights
- Feature gates در UI، API و سرویس‌ها enforced می‌شود.
- Checkout → Order → Wallet Payment → Payment record.
- Provider jobs با ثبت لاگ و context تننت اجرا می‌شوند.

## Observability
- Correlation ID از هدر دریافت یا تولید می‌شود و در Log context ثبت می‌گردد.
- هدر خروجی برای trace نمودن درخواست‌ها اضافه می‌شود.

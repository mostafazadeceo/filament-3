# معماری اتصال Provider eSIM Go

## خلاصه
این ماژول با الگوی Providers-Core پیاده‌سازی شده و از چهار پکیج مستقل تشکیل می‌شود:

1) `providers-esim-go-core`
- کلاینت HTTP، DTOها، mapping دامنه، سرویس‌های اصلی، مدل‌ها و جداول eSIM Go.
- ثبت Adapter در `Haida\ProvidersCore\Services\ProviderRegistry`.

2) `providers-esim-go-commerce`
- اتصال به `commerce-catalog`, `commerce-checkout`, `commerce-orders`.
- همگام‌سازی کاتالوگ و ساخت محصولات eSIM.
- ایجاد سفارش Provider پس از OrderPaid و مدیریت fulfillment.

3) `providers-esim-go-webhooks`
- دریافت callback، اعتبارسنجی HMAC بر raw-body، نرمال‌سازی رخدادها.
- location events صرفاً `ack` شده و ذخیره نمی‌شوند (No-surveillance).

4) `filament-providers-esim-go`
- UI پنل‌ها: اتصال، همگام‌سازی کاتالوگ، محصولات، سفارش‌ها، fulfillment، لاگ‌ها.

## نقاط اتصال کلیدی (Integration Points)
- Providers Core:
  - `Haida\ProvidersCore\Contracts\ProviderAdapter`
  - `Haida\ProvidersCore\Services\ProviderJobDispatcher`
  - `Haida\ProvidersCore\Models\ProviderJobLog`
- Commerce:
  - `Haida\CommerceCatalog\Models\CatalogProduct` / `CatalogVariant`
  - `Haida\CommerceCheckout\Services\CheckoutService`
  - `Haida\CommerceOrders\Events\OrderPaid`
- IAM/Permissions:
  - `Filamat\IamSuite\Contracts\CapabilityRegistryInterface`
  - `Filamat\IamSuite\Support\IamAuthorization`
- Notifications:
  - `Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher`
- Observability:
  - `app('correlation_id')` و ProviderJobLog

## جریان کلی (High-Level Flow)
Catalogue Sync → Product Publish → Checkout → Provider Order (validate/transaction) → Fulfillment → Webhooks → Usage Update → Notifications

## چندمستاجری (Tenancy)
- همه مدل‌ها `BelongsToTenant` هستند.
- همه پرس‌وجوها بر اساس `tenant_id` اسکوپ می‌شوند.
- Jobها با `TenantContext` اجرا می‌شوند.

## امنیت
- HMAC sha256 base64 روی raw body.
- Signature header قابل پیکربندی است.
- Rate limit کلاینت: 10 TPS با RateLimiter.
- Retry برای 429 و 503 با احترام به Retry-After.

## No-Surveillance
- رویدادهای location/position در Webhook فقط 200 OK می‌گیرند و ذخیره نمی‌شوند.


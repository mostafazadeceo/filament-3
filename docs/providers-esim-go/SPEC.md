# SPEC — Provider eSIM Go

## اهداف
- اتصال امن و ماژولار eSIM Go به پلتفرم چندمستاجره.
- همگام‌سازی کاتالوگ بدون polling مکرر.
- سفارش و fulfillment غیرهمزمان با تحمل تاخیر ۱۰ دقیقه‌ای.
- وبهوک‌های امن با HMAC و بدون ذخیره داده‌های Location.

## مدل دامنه
- Connection: تنظیمات اتصال و API Key.
- Catalogue Snapshot: کش کاتالوگ با hash برای جلوگیری از آپدیت غیرضروری.
- Product: نگاشت bundle به محصول فروشگاهی.
- Order: سفارش Provider در کنار commerce_order.
- eSIM: اطلاعات فعال‌سازی (iccId / smdpAddress / matchingId).
- Callback: رخدادهای دریافتی با امضای معتبر.
- Inventory Usage: وضعیت مصرف بسته‌ها.

## State Machine (Order)
- pending → provisioning → ready → delivered
- failed (در validate یا transaction)
- retryable: 429/503 با backoff

## Capability Matrix (خلاصه)
| Endpoint | هدف | Idempotency | Retry | Mapping |
|---|---|---|---|---|
| GET /catalogue | دریافت کاتالوگ | cache+hash | 429/503 | esim_go_products + catalog_product |
| POST /orders type=validate | اعتبارسنجی سفارش | dedupe by commerce_order_id | 429/503 | validate only |
| POST /orders type=transaction | ایجاد سفارش | dedupe by commerce_order_id | 429/503 | esim_go_orders + esims |
| GET /inventory | موجودی بسته‌ها | cache TTL | 429/503 | esim_go_inventory_usages |
| POST /inventory/refund | Refund (اختیاری) | idempotency_key | 429/503 | inventory usage update |
| Callback (V2/V3) | رخداد مصرف/تغییر | idempotent by signature+hash | N/A | callbacks + notify |

## Auth و Rate Limit
- Header: X-API-Key (قابل تنظیم در config).
- 10 TPS: RateLimiter داخلی + queue throttling.
- 503 Retry-After: رعایت دقیق header.

## Mapping به Commerce
- Product type: `digital_code`.
- track_inventory = false.
- metadata شامل provider=esim-go و bundle_name و مشخصات باندل.
- قیمت‌ها در صورت نیاز با FX به ارز سایت تبدیل می‌شوند؛ اگر نرخ موجود نباشد، با تنظیم `force_site_currency` قیمت با ارز سایت ذخیره می‌شود و مقدار اصلی در metadata حفظ می‌گردد.
- OrderPaid → validate → transaction → fulfillment (async).

## No-Surveillance
- Location events: فقط 200 OK و بدون ذخیره داده.

## Permissions (نمونه)
- esim_go.connection.view / manage
- esim_go.catalogue.view / sync
- esim_go.order.view / manage
- esim_go.fulfillment.view
- esim_go.webhook.view
- esim_go.inventory.view / refund

## خطاها و Failure Modes
- 401/403: API Key نامعتبر.
- 429/503: retry با backoff.
- bundle name حساس به حروف.
- assign ممکن است تا ۱۰ دقیقه طول بکشد → polling محدود.

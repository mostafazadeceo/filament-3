# امنیت — eSIM Go

## HMAC
- الگوریتم: HMAC-SHA256
- ورودی: raw-body
- خروجی: Base64
- کلید: API Key اتصال
- هدر امضا: configurable list

## Idempotency
- سفارش‌ها: برای هر `commerce_order_id` فقط یک سفارش Provider ایجاد می‌شود مگر با فلگ داخلی `force`.
- وبهوک‌ها: `payload_hash` (sha1 raw-body) + signature برای جلوگیری از پردازش تکراری.

## Rate limit
- 10 TPS per tenant.
- 429/503 با backoff و Retry-After.

## No-Surveillance
- Location/position events ذخیره نمی‌شود.

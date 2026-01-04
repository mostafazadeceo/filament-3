# امنیت و حاکمیت — پلتفرم رمزارز

## مدل تهدید
- جعل وبهوک و تغییر وضعیت پرداخت
- افشای کلیدهای API/Secret
- حملات تکرار (Replay) و دوباره‌پردازش
- ناهماهنگی وضعیت بین وبهوک و بلاک‌چین
- سوءاستفاده از برداشت‌ها یا مقصدهای غیرمجاز

## کنترل‌های کلیدی
- رمزنگاری Secrets با Castهای encrypted
- بررسی امضا برای همه وبهوک‌ها
- IP Allowlist برای Cryptomus و CoinPayments
- Idempotency برای `order_id` و `event_id`
- Queue-based processing + DLQ برای وبهوک
- Rate limit API (پیش‌فرض 60,1)
- ثبت Audit log دامنه مالی
- لیست سفید مقصد برداشت + Approval قبل از ارسال

## وبهوک‌ها
- Cryptomus: `sign` در بدنه + IP allowlist
- Coinbase Commerce: `X-CC-Webhook-Signature` (HMAC-SHA256)
- CoinPayments: `X-CoinPayments-Signature` (HMAC-SHA512) + IP allowlist
- BTCPay: `BTCPay-Sig` (HMAC-SHA256)

## مدیریت کلیدها
- کلیدها فقط در `CryptoProviderAccount` ذخیره و رمزنگاری می‌شوند.
- لاگ‌ها باید redact شوند؛ خروجی Raw Payload فقط برای DLQ/Debug محدود.
- اطلاعات RPC نودها در `CryptoNodeConnector.config_json` به‌صورت رمزنگاری‌شده نگهداری می‌شود.

## برداشت و تسویه
- قابلیت whitelist مقصد و Approval workflow
- محدودیت برداشت بر اساس پلن و Feature flag

## حریم خصوصی و عدم نظارت
- هیچ داده نظارتی یا پروفایلینگ انسانی ذخیره نمی‌شود.
- AI Auditor فقط تحلیل مالی و anomaly گزارش می‌دهد.

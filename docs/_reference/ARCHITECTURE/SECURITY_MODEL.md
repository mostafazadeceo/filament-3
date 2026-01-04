# SECURITY_MODEL

## تهدیدها (خلاصه)
- دسترسی غیرمجاز به داده‌های tenant دیگر (cross-tenant leakage).
- جعل درخواست API یا سوءاستفاده از API key.
- تکرار وبهوک و دوباره‌پردازش مالی.
- نشت secrets (توکن‌ها/کلیدهای ارائه‌دهندگان).
- تزریق داده مخرب در وبهوک‌ها یا payloadها.

## کنترل‌ها
- IAM با scope و تیم‌بندی (`spatie/laravel-permission` + teams).
- Middlewareهای API: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, `filamat-iam.scope:*`.
- Policyها برای منابع حساس (پرداخت، وبهوک، مالی).
- API نرخ ارز با توکن اختصاصی و محدودسازی نرخ درخواست محافظت می‌شود.
- OpenAPI ماژول‌ها با scopeهای مشاهده ماژول محدود شده است.
- اندروید: ثبت دستگاه و توکن اعلان (FCM) در مسیرهای امن App API.
- اندروید: Play Integrity token در ورود (در صورت فعال بودن feature flag).

## مدیریت Secrets
- مقادیر حساس از طریق env و config مدیریت می‌شوند.
- استفاده از castهای رمزنگاری در موارد حساس (نمونه: `EncryptedCast` در ماژول نرخ ارز).
- [ASSUMPTION] کلیدها در vault یا secret manager ذخیره شوند.

## وبهوک‌ها و Replay Protection
- امضای HMAC برای ارائه‌دهندگان (Payments/Orchestrator).
- nonce/کد یکتا و window زمانی برای جلوگیری از replay.
- idempotency key برای عملیات حساس.
- وبهوک‌های پرداخت با یکتاسازی بر مبنای tenant/provider/external_id از تداخل بین tenantها جلوگیری می‌کنند.

## Audit Logging
- IAM Suite جدول‌های audit و security events دارد.
- مرز audit: عملیات مدیریتی، تغییرات دسترسی، عملیات مالی و وبهوک‌ها.

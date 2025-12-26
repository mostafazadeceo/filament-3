# معماری Filamat IAM Suite

## اهداف
- ارائه سکوی مدیریت هویت و دسترسی (IAM)، کیف پول و اشتراک برای همه ماژول‌ها با امکان توسعه‌پذیری.
- پشتیبانی همزمان از پنل سوپرادمین و پنل‌های تننت.
- ارائه ای‌پی‌آی نسخه‌بندی‌شده و وبهوک‌های ورودی/خروجی.
- ثبت کامل رویدادهای امنیتی و ممیزی.

## محدوده
- مدیریت کاربران، نقش‌ها، مجوزها، گروه‌ها، بازنویسی دسترسی‌ها.
- کیف پول چندارزی با تراکنش‌ها و هولد.
- پلن‌ها و اشتراک‌ها برای تننت و کاربر.
- مرکز اعلان‌ها و رمز یکبارمصرف از طریق مبدل.
- وبهوک‌های اعلان و پرداخت.

## خارج از محدوده
- پیاده‌سازی کامل درگاه‌های پرداخت واقعی (فقط Dummy Provider).
- سیستم رابط کاربری عمومی برای بخش کاربری غیرادمین.
- مدیریت فایل‌ها یا ذخیره‌سازی خارج از محدوده.

## مدل داده (خلاصه)
- `organizations` ← سازمان‌ها و حالت اشتراک داده.
- `tenants` ← فضاهای کاری (با `settings` و `owner`).
- `tenant_user` ← عضویت کاربران در تننت با نقش/وضعیت.
- `roles/permissions` ← مبتنی بر Spatie با `tenant_id`.
- `groups` و جداول پیوند گروه-کاربر/نقش/مجوز.
- `permission_overrides` ← بازنویسی‌های کاربر.
- `wallets`, `wallet_transactions`, `wallet_holds`.
- `subscription_plans`, `subscriptions`.
- `webhooks`, `webhook_deliveries`.
- `notifications`, `otp_codes`.
- `audit_logs`, `security_events`.
- `api_keys`.

## مدل دسترسی (کنترل مبتنی بر نقش + گروه + بازنویسی)
- لایه‌ها: نقش‌ها + گروه‌ها + بازنویسی‌های کاربر.
- تقدم تصمیم: بازنویسی کاربر (عدم اجازه) > بازنویسی کاربر (اجازه) > گروه (عدم اجازه) > گروه (اجازه) > نقش.
- مجوزهای زمان‌دار از طریق `expires_at`.
- پس از تصمیم نهایی، گیت اشتراک اعمال می‌شود: نبود اشتراک فعال یا نبود مجوز در پلن می‌تواند دسترسی را رد کند.
- مسیر شفاف‌سازی در «شبیه‌ساز دسترسی» نمایش داده می‌شود.

## چندتننتی و حالت اشتراک داده
- اسکوپ تننت با `TenantScope` و `TenantContext` اعمال می‌شود.
- حالت اشتراک داده: `shared_by_organization` با لیست مدل‌های مشترک از کانفیگ.
- پنل سوپرادمین از اسکوپ تننت عبور می‌کند.
- در حالت امپرسونیشن، عبور از اسکوپ غیرفعال می‌شود تا کاربر دقیقاً در زمینه تننت عمل کند.

## ای‌پی‌آی (نمای کلی)
- نسخه: `/api/v1/*`
- احراز هویت: توکن Sanctum یا `X-Api-Key`.
- محدودسازی نرخ: مقدار کانفیگ `filamat-iam.api.rate_limit`.
- مسیرها: `/tenants`, `/users`, `/roles`, `/permissions`, `/groups`, `/wallets`, `/transactions`, `/wallet-holds`, `/plans`, `/subscriptions`, `/notifications/send`.
- عملیات کیف‌پول: `/wallets/{id}/credit`, `/wallets/{id}/debit`, `/wallets/{id}/holds`, `/wallets/transfer`, `/wallet-holds/{id}/capture`, `/wallet-holds/{id}/release`.
- وبهوک ورودی: `/api/v1/webhooks/notification-plugin` و `/api/v1/webhooks/payment-provider`.
- اسکوپ‌ها با middleware `filamat-iam.scope` و توانایی‌های Sanctum/API Key اعمال می‌شوند.

## قرارداد ادغام اعلان/رمز یکبارمصرف
- مبدل با این متدها:
  - `sendOtp(user, purpose, code, meta)`
  - `sendNotification(target, type, payload)`
  - `handleWebhook(payload, headers)`
- در حالت `custom_plugin` رویدادهای `OtpRequested` و `NotificationRequested` منتشر می‌شوند.

## قرارداد توسعه‌پذیری
- رجیستری قابلیت‌ها: `CapabilityRegistryInterface`.
- ماژول‌ها می‌توانند مجوزها/ویژگی‌ها/کوئوتاها را ثبت کنند.
- رویدادها: `CapabilityRegistered`, `TenantCreated`, `UserInvited`, `SubscriptionChanged`.
- دستور `php artisan filamat-iam:sync` برای همگام‌سازی مجوزها.

## مدل امنیت
- محدودسازی نرخ ای‌پی‌آی.
- رمز یکبارمصرف با محدودیت تلاش، قفل موقت و Rate Limit.
- وبهوک‌ها با امضای HMAC، تحمل زمانی و کلید یکتا (Idempotency Key).
- ثبت ممیزی و رویدادهای امنیتی.
- قابلیت چرخش کلید ای‌پی‌آی.
- امپرسونیشن با ثبت ممیزی و رویداد امنیتی شروع/پایان.

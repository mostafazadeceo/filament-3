# راهنمای TLS و صدور گواهی

## پیش‌نیازها
- فعال‌سازی TLS: `TENANCY_DOMAINS_TLS_ENABLED=true`
- انتخاب Provider: `TENANCY_DOMAINS_TLS_PROVIDER`
- تنظیمات ACME (در صورت استفاده):
  - `TENANCY_DOMAINS_ACME_DIRECTORY`
  - `TENANCY_DOMAINS_ACME_EMAIL`

## وضعیت‌ها
- `not_requested`: هنوز درخواست صدور ثبت نشده است
- `pending`: درخواست ثبت شده و در صف صدور است
- `issued`: گواهی صادر شده است
- `failed`: صدور ناموفق (نیاز به بررسی)

## درخواست دستی TLS
- از پنل: اکشن «درخواست TLS» برای دامنه تایید شده
- از API:
  - `POST /api/v1/tenancy-domains/domains/{id}/request-tls`

## تمدید خودکار
- دستور CLI:
  - `php artisan tenancy-domains:renew-certificates`
- پیشنهاد زمان‌بندی (Cron):
  - روزانه یک‌بار، یا هر ۱۲ ساعت

## عیب‌یابی سریع
- دامنه باید حتماً `verified` باشد.
- اگر Provider تنظیم نشده باشد، وضعیت به `failed` تبدیل می‌شود.
- خطای دقیق در فیلد `tls_error` ثبت می‌شود.

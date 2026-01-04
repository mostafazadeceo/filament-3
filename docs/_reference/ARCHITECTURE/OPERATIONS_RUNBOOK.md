# OPERATIONS_RUNBOOK

## تصویر کلی عملیاتی
- اپلیکیشن Laravel + Filament v4 با ماژول‌های package-first.
- صف برای وبهوک‌ها، اعلان‌ها و عملیات سنگین.
- پایگاه داده مرکزی با جداول متعدد چند‌مستاجری.

## مراحل Deploy (Staging/Prod)
1) Pull/Deploy کد و اطمینان از سلامت composer/npm.
2) `composer install --no-dev --optimize-autoloader`
3) `php artisan migrate --force`
4) `php artisan filamat-iam:sync --guard=web`
5) `php artisan config:cache && php artisan route:cache` (در صورت استفاده)
6) ریست سرویس queue/workers.
7) بررسی پنل‌های Admin/Tenant و API سلامت.

## Rollback Strategy
- بازگشت به نسخه قبلی کد و اجرای migrate rollback اگر امکان‌پذیر است.
- اگر rollback دیتابیس ممکن نیست، اعلام incident و اجرای hotfix.

## Incident Playbooks
### Webhook Backlog
- بررسی صف‌های وبهوک، وضعیت worker و نرخ شکست.
- افزایش موقت workerها و بررسی idempotency.
- در صورت تکرار خطا، قطع موقت ingestion و reprocess کنترل‌شده.

### Queue Stuck
- بررسی سلامت Redis/DB queue، restart workerها.
- بررسی jobهای failed و تصمیم برای retry.

### Payment/Provider Outage
- فعال‌سازی fallback (در صورت وجود).
- توقف موقت درخواست‌های حساس و نمایش پیام به tenant.
- ثبت incident و پیگیری با provider.

### Tenant Cross-Scope Incident
- مسدود کردن access token/API key.
- بررسی audit logs و بازسازی دسترسی‌ها.
- اطلاع‌رسانی امنیتی و اعمال patch.

## Alerts
- هر alert باید runbook مرتبط داشته باشد و دوره‌ای تست شود.
- نمونه‌ها: `webhook_fail_rate`, `queue_backlog`, `payment_error_rate`, `auth_scope_denied_spike`.

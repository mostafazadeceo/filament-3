# راهنمای اجرای استیجینگ (Staging Runbook)

## هدف
اجرای امن سناریوهای end-to-end روی محیط استیجینگ بدون ریسک برای تولید.

## پیش‌نیازها
- عدم اجرا روی Production (اسکریپت با APP_ENV=production متوقف می‌شود)
- دیتابیس جداگانه برای استیجینگ
- دسترسی نوشتن روی دیتابیس استیجینگ

## اجرای سریع با SQLite (ایمن‌ترین حالت)
```bash
APP_ENV=staging \
DB_CONNECTION=sqlite \
SQLITE_PATH=/tmp/haida_staging.sqlite \
CACHE_STORE=array \
QUEUE_CONNECTION=sync \
./scripts/staging-e2e.sh
```

## اجرای استیجینگ با دیتابیس واقعی (نیازمند تایید مهاجرت)
> این مسیر روی دیتابیس واقعی استیجینگ می‌نویسد.

```bash
APP_ENV=staging \
DATABASE_URL="mysql://user:pass@host:3306/haida_staging" \
CACHE_STORE=array \
QUEUE_CONNECTION=sync \
STAGING_ALLOW_MIGRATE=1 \
./scripts/staging-e2e.sh
```

## خروجی‌های مورد انتظار
- اجرای `php artisan test`
- اجرای `./scripts/demo-e2e.sh`
- اجرای `php scripts/deep_scenario_runner.php`

## نکات ایمنی
- هرگز روی production اجرا نکنید.
- برای دیتابیس واقعی، حتماً از STAGING_ALLOW_MIGRATE=1 استفاده کنید.
- در صورت نیاز به پاکسازی داده‌های استیجینگ، از نسخه پشتیبان استفاده کنید.


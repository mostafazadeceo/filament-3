# یادداشت‌های استقرار (Deployment Notes)

## پیش‌نیازها
- تنظیم `APP_ENV`, `APP_KEY`, `APP_URL`
- تنظیم اتصال دیتابیس و کش
- اجرای مهاجرت‌ها با `php artisan migrate --force`

## مراحل پیشنهادی
1) اجرای `./scripts/release-checklist-verify.sh`
2) اجرای `php artisan test`
3) اجرای `./scripts/qa-sanity.sh`
4) اجرای `./scripts/staging-e2e.sh`
5) Deploy و مانیتورینگ لاگ‌ها

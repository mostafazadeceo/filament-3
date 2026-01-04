# چک‌لیست QA نهایی

## اجرای سریع
```bash
APP_ENV=staging ./scripts/qa-sanity.sh
```

## پوشش‌ها
- بررسی فرمت کد (Laravel Pint)
- تست‌های واحد/ویژگی (`php artisan test`)
- سناریوی demo-e2e
- deep_scenario_runner برای جریان‌های چندماژوله

## نکات
- اجرای این چک‌لیست روی production ممنوع است.

# Runbook — Mailtrap

## مانیتورینگ
- لاگ‌های API در `storage/logs/laravel.log` با کلیدهای `mailtrap.http` و `mailtrap.send` ثبت می‌شوند.
- از پنل Filament، وضعیت اتصال، Inbox و دامنه‌ها قابل مشاهده است.

## همگام‌سازی
- همگام‌سازی Inbox و دامنه‌ها به صورت دستی (UI) یا API قابل اجراست.
- برای جلوگیری از polling مکرر، حداقل فاصله زمانی Sync در `MAILTRAP_SYNC_MIN_SECONDS` تنظیم می‌شود.

## خطاها
- 429/503 به صورت retry با backoff مدیریت می‌شود.
- اگر توکن معتبر نبود، خطای `mailtrap_missing_token` برمی‌گردد.

## پیشنهاد عملیاتی
- برای هر Tenant یک اتصال Mailtrap ثبت کنید.
- در صورت داشتن Send API جدا، `send_api_token` را جداگانه ذخیره کنید.


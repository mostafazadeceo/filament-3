# AUTOPILOT STATE

## آخرین اقدام
- تکمیل PR-052 (یکپارچه‌سازی eSIM Go Provider + تثبیت deep runner برای موجودی).
- اجرای تست‌ها:
  - `php artisan test`
  - `bash scripts/demo-e2e.sh`
  - `php scripts/deep_scenario_runner.php`

## گام بعدی
در صورت نیاز عملیاتی: تنظیم API Key و هدر امضای وبهوک eSIM Go در محیط واقعی.

## نکات اجرایی
- بدون استفاده از git.
- پس از هر PR: به‌روزرسانی `docs/STATUS.md`, `docs/AUTOPILOT-STATE.md`, `docs/99-release-checklist.md`.

## آخرین اقدام (PR-055)
- رفع ارورهای 500 ناشی از کلاس‌های Petty Cash و جدول‌های ThreeCX.
- بهبود همگام‌سازی eSIM Go و پیام‌های خطا.
- تثبیت مسیر asset های Livewire.
- اجرای:
  - `composer dump-autoload`
  - `php artisan migrate --force --path=packages/filament-threecx/database/migrations`
  - `php artisan optimize:clear`
  - `truncate -s 0 storage/logs/laravel.log`

## گام بعدی
- بررسی مجدد لاگ‌های جدید بعد از بازکردن صفحات مشکل‌دار و همگام‌سازی eSIM/Inbox.

## آخرین اقدام (PR-054)
- اصلاح tap در عملیات ClockIn/ClockOut و درایور حضور و غیاب (رفع TypeError).
- افزودن Fake mode برای eSIM Go, Mailtrap, و HMAC Gateway جهت اجرای سناریوها بدون وابستگی خارجی.
- تثبیت deep_scenario_runner با فعال‌سازی fake modes و اجرای کامل سناریوها.
- اجرای:
  - `php scripts/deep_scenario_runner.php`
  - `php artisan test`
  - `./scripts/demo-e2e.sh`
  - `truncate -s 0 storage/logs/laravel.log`

## گام بعدی
- پایش لاگ‌ها بعد از تست UI همگام‌سازی‌ها و صفحه محصولات eSIM.

## آخرین اقدام (PR-053)
- افزودن محافظ برای جدول‌های PAM در filamat-iam-suite (جلوگیری از خطای نبود جدول در تست/بوت).
- جلوگیری از خطای ثبت منابع Commerce Orders در صورت نبود کلاس‌ها.
- رفع خطای فعال‌سازی اعلان وب‌پوش (اضافه‌کردن credentials و مدیریت خطای پاسخ).
- بهبود نمایش محصولات eSIM (گروه‌بندی، شمارش کشورها، نمایش سهمیه‌ها و حجم خواناتر).
- انتشار فایل‌های livewire برای جلوگیری از 404 روی livewire.min.js.
- اجرای تست‌ها:
  - `php artisan test --filter=CorrelationIdFactoryTest`
  - `php artisan test` (timeout بعد از 120s؛ Failهای متعدد ثبت شد)

## گام بعدی
- بررسی Failهای تست سراسری و تکمیل seed/fixtures.
- بررسی دوباره لاگ‌های تولید بعد از بازکردن صفحات مشکل‌دار.

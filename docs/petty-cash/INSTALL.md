# نصب ماژول تنخواه

## پیش‌نیازها
- Laravel 11+
- Filament v4
- filamat/filamat-iam-suite
- vendor/filament-accounting-ir

## نصب (محلی)
1) مسیر پکیج به Composer اضافه شده است (path repository).
2) پکیج را در `composer.json` پروژه ثبت کنید:
   - `haida/filament-petty-cash-ir`.
3) انتشار تنظیمات (اختیاری):
   - `php artisan vendor:publish --tag=filament-petty-cash-ir-config`
4) اجرای مهاجرت‌ها:
   - `php artisan migrate --force`

## فعال‌سازی پنل‌ها
- ماژول با `FilamentPettyCashIrPlugin` در پنل‌ها ثبت می‌شود.

## حسابداری
- برای ثبت خودکار اسناد، حساب‌های تنخواه و منبع تغذیه را در صندوق‌ها مشخص کنید.
- در صورت عدم تنظیم، ثبت خودکار سند حسابداری انجام نمی‌شود.

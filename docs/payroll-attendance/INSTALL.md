# نصب Payroll Attendance IR

## پیش‌نیازها
- PHP 8.4+
- Laravel 12+
- Filament v4
- Filamat IAM Suite
- افزونه حسابداری ایران (`vendor/filament-accounting-ir`)

## نصب پکیج
```bash
composer require vendor/filament-payroll-attendance-ir
php artisan migrate
```

## فعال‌سازی در پنل‌ها
در پنل Admin و Tenant پلاگین زیر را اضافه کنید:
`Vendor\FilamentPayrollAttendanceIr\FilamentPayrollAttendanceIrPlugin::make()`

## تنظیمات اولیه
1) جداول قانونی را ثبت کنید:
   - حداقل دستمزد
   - مزایا (مسکن/بن/اولاد/تاهل/سنوات)
   - بیمه (نرخ‌ها و سقف)
   - مالیات (معافیت و پلکان)
2) کارکنان و قراردادهای رسمی/داخلی را ثبت کنید.
3) شیفت‌ها و برنامه شیفت را تنظیم کنید.
4) ثبت تردد را انجام دهید یا از API استفاده کنید.
5) کارکردها را تایید و دوره حقوق را محاسبه کنید.

## API
- مسیر API: `/api/v1/payroll-attendance`
- OpenAPI: `/api/v1/payroll-attendance/openapi`
- مجوزها از طریق IAM و اسکوپ‌های `payroll.*` کنترل می‌شوند.

# نصب ماژول HR Attendance

## پیش‌نیازها
- PHP 8.2+
- Laravel 11+
- Filament v4
- filamat/filamat-iam-suite
- vendor/filament-accounting-ir

## نصب پکیج
1) مسیر پکیج به Composer اضافه شده است (path repository).
2) پکیج را در `composer.json` ثبت کنید:
   - `haida/filament-payroll-attendance-ir`
3) انتشار تنظیمات (اختیاری):
   - `php artisan vendor:publish --tag=filament-payroll-attendance-ir-config`
4) اجرای مهاجرت‌ها:
   - `php artisan migrate --force`

## فعال‌سازی پنل‌ها
- ماژول از طریق `FilamentPayrollAttendanceIrPlugin` در پنل‌های Admin/Tenant ثبت می‌شود.

## تنظیمات کلیدی
- `filament-payroll-attendance-ir.privacy.*` برای مکان/بیومتریک و لاگ دسترسی حساس.
- `filament-payroll-attendance-ir.policy.*` برای قوانین حضور، گراس‌پریود، سقف اضافه‌کار.
- `filament-payroll-attendance-ir.fraud.*` برای کنترل‌های ضدتقلب.
- `filament-payroll-attendance-ir.ai.*` برای فعال‌سازی AI و ارائه‌دهنده.

## مجوزهای کلیدی
- سیاست‌ها: `payroll.policy.view`, `payroll.policy.manage`
- رویدادهای زمانی: `payroll.time_event.view`, `payroll.time_event.manage`
- کاربرگ‌ها: `payroll.timesheet.view`, `payroll.timesheet.manage`, `payroll.timesheet.approve`
- درخواست‌ها: `payroll.leave.*`, `payroll.mission.*`, `payroll.overtime.*`
- رضایت و ممیزی: `payroll.consent.*`, `payroll.audit.view`
- گزارش‌ها: `payroll.report.view`, `payroll.report.export`
- AI: `payroll.ai.use`, `payroll.ai.view`

## درایورهای ثبت حضور
- Web: `WebDriver`
- Mobile: `MobileDriver`
- Kiosk: `KioskDriver`
- Hardware: `HardwareDeviceDriver`

انتخاب درایور از طریق پارامتر `source` یا `driver` در API ثبت رویداد انجام می‌شود.

## حریم خصوصی
- مکان فقط در بازه ورود/خروج و با رضایت صریح ذخیره می‌شود.
- بیومتریک Opt-in است و داده خام ویدئویی ذخیره نمی‌شود.

## OpenAPI
- خروجی: `GET /api/v1/payroll-attendance/openapi`

# یادداشت‌های ارتقا — HR Attendance

## مهاجرت‌های دیتابیس
- جداول جدید:
  - `payroll_departments`, `payroll_positions`
  - `payroll_attendance_policies`, `payroll_work_calendars`, `payroll_holiday_rules`
  - `payroll_time_events`, `payroll_time_breaks`, `payroll_timesheets`
  - `payroll_overtime_requests`, `payroll_attendance_exceptions`
  - `payroll_employee_consents`, `payroll_sensitive_access_logs`
  - `payroll_ai_logs`
- ستون‌های جدید:
  - `payroll_employees.department_id`, `payroll_employees.position_id`

اجرای مهاجرت‌ها:
- `php artisan migrate --force`

## مجوزها
- سیاست‌های حضور: `payroll.policy.view`, `payroll.policy.manage`
- رویدادهای زمانی: `payroll.time_event.view`, `payroll.time_event.manage`
- رکوردهای حضور: `payroll.attendance.view`, `payroll.attendance.manage`, `payroll.attendance.approve`, `payroll.attendance.lock`
- کاربرگ‌ها: `payroll.timesheet.view`, `payroll.timesheet.manage`, `payroll.timesheet.approve`
- استثناها: `payroll.exception.view`, `payroll.exception.manage`, `payroll.exception.resolve`
- تقویم کاری: `payroll.calendar.view`, `payroll.calendar.manage`
- مرخصی: `payroll.leave.view`, `payroll.leave.request`, `payroll.leave.approve`, `payroll.leave.manage`
- ماموریت: `payroll.mission.view`, `payroll.mission.request`, `payroll.mission.approve`, `payroll.mission.manage`
- اضافه‌کار: `payroll.overtime.view`, `payroll.overtime.request`, `payroll.overtime.approve`, `payroll.overtime.manage`
- رضایت‌نامه‌ها: `payroll.consent.view`, `payroll.consent.manage`
- ممیزی: `payroll.audit.view`
- گزارش‌ها: `payroll.report.view`, `payroll.report.export`
- AI: `payroll.ai.use`, `payroll.ai.view`

## API جدید
- `attendance-policies`, `time-events`, `attendance-exceptions`
- `timesheets`, `timesheets/generate`, `timesheets/{timesheet}/approve`
- `work-calendars`, `holiday-rules`
- `mission-requests`, `overtime-requests`
- `employee-consents`, `sensitive-access-logs`, `ai-logs`
- `reports/*`, `reports/ai/manager`
- OpenAPI: `/api/v1/payroll-attendance/openapi`

## تنظیمات جدید
- `filament-payroll-attendance-ir.privacy.*`
- `filament-payroll-attendance-ir.policy.*`
- `filament-payroll-attendance-ir.fraud.*`
- `filament-payroll-attendance-ir.ai.*`

## ملاحظات حریم خصوصی
- مکان و بیومتریک تنها با رضایت صریح ذخیره می‌شود.
- لاگ دسترسی حساس به صورت پیش‌فرض فعال است (قابل الزام به دلیل).

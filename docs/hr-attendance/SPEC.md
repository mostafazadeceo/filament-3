# SPEC — ماژول منابع انسانی و حضور و غیاب (HR Attendance)

## شمال ستاره
ساخت یک ماژول حضور و غیاب و منابع انسانی «سطح سازمانی» با معماری تمیز، چندمستاجری امن، تجربه فارسی، و قابلیت توسعه برای اپلیکیشن‌های موبایل/وب — بدون هرگونه نظارت پنهانی.

## اهداف
- ثبت دقیق رویدادهای ورود/خروج و محاسبه کارکرد در سطح شرکت/شعبه/کارمند.
- سیاست‌های منعطف حضور: شیفت‌ها، تقویم کاری، گراس‌پریود، قواعد استراحت، اضافه‌کار، دورکاری.
- کنترل‌های ضدتقلب غیرنظارتی: تشخیص الگوهای غیرممکن و ثبت استثناها.
- صندوقچه استثناها (Exception Inbox) با مسئولیت‌پذیری و ثبت یادداشت حل.
- لایه AI اختیاری و شفاف برای گزارش مدیریتی و پیشنهادهای سیاستی.
- API پایدار برای اپ‌ها و یکپارچگی با سخت‌افزار/کاتالوگ دستگاه‌ها.

## غیرهدف‌ها
- هر نوع پایش مداوم (CCTV/RTSP) یا ردیابی دائمی رفتار کارکنان.
- امتیازدهی یا رتبه‌بندی کارکنان با AI.

## مدل دامنه
- EmployeeProfile, Department, Position
- AttendancePolicy, ShiftPattern, WorkCalendar, HolidayRule
- TimeEvent, TimeBreak, Timesheet
- LeaveRequest, MissionRequest, OvertimeRequest
- AttendanceException, AuditEvent
- EmployeeConsent, SensitiveAccessLog, AiLog

## Use-Caseهای کلیدی
- ClockIn/ClockOut با اعمال سیاست‌ها و ثبت استثناها.
- AssignShift و تولید Timesheet دوره‌ای.
- Request/Approve Leave/Mission و انطباق با سیاست‌ها.
- RaiseException/ResolveException با یادداشت اجباری و مسئول مشخص.
- ExportReports برای گزارش‌های مدیریتی.

## معماری لایه‌ای
- Domain: مدل‌ها، Enumها و ماشین حالت درخواست‌ها/تاییدها.
- Application: Use-Caseها و سرویس‌های سیاست/ضدتقلب/گزارش.
- Infrastructure: درایورها و آداپترهای ثبت حضور (Web/Mobile/Kiosk/Hardware).
- UI (Filament v4): منابع HR، حضور و استثناها با مجوزهای ریز.
- API v1: مسیرهای پایدار با Middlewareهای IAM.

## سیاست‌ها و ضدتقلب (بدون نظارت)
- قوانین: geofence, wifi, device attestation, گراس‌پریود، سقف اضافه‌کار.
- کنترل‌ها: فاصله زمانی حداقل بین رویدادها، سرعت جابجایی غیرممکن، اصلاح دستی با دلیل.
- تمام استثناها به Inbox ارسال و حل با دلیل ثبت می‌شود.

## حریم خصوصی و انطباق
- رضایت صریح برای مکان و بیومتریک.
- جمع‌آوری داده مکانی فقط بین ورود/خروج و قابل غیرفعال‌سازی.
- ثبت لاگ دسترسی به داده‌های حساس با دلیل.
- بیومتریک صرفاً Opt-in؛ بدون ذخیره ویدئو.

## AI (اختیاری، شفاف)
- گزارش مدیریتی فارسی (دوره‌ای).
- پیشنهاد اصلاح سیاست‌ها بر اساس استثناها.
- تشخیص ناهنجاری صرفاً در سطح تراکنش/کنترل (بدون رتبه‌بندی کارکنان).

## API v1 (خلاصه)
- `/api/v1/payroll-attendance/attendance-policies`
- `/api/v1/payroll-attendance/time-events`
- `/api/v1/payroll-attendance/timesheets`
- `/api/v1/payroll-attendance/work-calendars`
- `/api/v1/payroll-attendance/holiday-rules`
- `/api/v1/payroll-attendance/mission-requests`
- `/api/v1/payroll-attendance/overtime-requests`
- `/api/v1/payroll-attendance/employee-consents`
- `/api/v1/payroll-attendance/sensitive-access-logs`
- `/api/v1/payroll-attendance/ai-logs`
- `/api/v1/payroll-attendance/attendance-exceptions`
- `/api/v1/payroll-attendance/reports/*`
- `/api/v1/payroll-attendance/openapi`

## فرضیات
- Filamat IAM Suite برای احراز هویت/مجوزها فعال است.
- سیستم حسابداری و ساختار شرکت/شعبه در ماژول حسابداری موجود است.

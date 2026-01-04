# مبنای ارتقای HR + Attendance (M0)

## مرز ماژول و وضعیت فعلی
- ماژول فعال: `packages/filament-payroll-attendance-ir` با نام پکیج `vendor/filament-payroll-attendance-ir` و ثبت در `composer.json`.
- پلاگین Filament ثبت شده در پنل‌ها: `app/Providers/Filament/AdminPanelProvider.php` و `app/Providers/Filament/TenantPanelProvider.php` با `FilamentPayrollAttendanceIrPlugin`.
- پکیج موازی/قدیمی: `packages/filament-payroll-attendance` (اسکلت و مسیر API خالی، منابع ثبت نشده).
- مستندات قبلی ماژول: `docs/payroll-attendance/*`.

## نقشه دامنه (مدل‌ها + جدول‌ها)
منبع: `packages/filament-payroll-attendance-ir/src/Models`, `packages/filament-payroll-attendance-ir/src/Domain/Models`, مهاجرت‌ها.

هسته HR/سازمان
- کارمند: `PayrollEmployee` -> `payroll_employees`.
- مدارک کارمند: `PayrollEmployeeDocument` -> `payroll_employee_documents`.
- دپارتمان: `Department` -> `payroll_departments`.
- پست سازمانی: `Position` -> `payroll_positions`.

حضور و غیاب (عملیاتی)
- شیفت: `PayrollAttendanceShift` -> `payroll_attendance_shifts`.
- برنامه: `PayrollAttendanceSchedule` -> `payroll_attendance_schedules`.
- پانچ: `PayrollTimePunch` -> `payroll_time_punches`.
- کارکرد: `PayrollAttendanceRecord` -> `payroll_attendance_records`.
- تعطیلات: `PayrollHoliday` -> `payroll_holidays`.

حضور و غیاب (دامنه)
- سیاست حضور و غیاب: `AttendancePolicy` -> `payroll_attendance_policies`.
- تقویم کاری: `WorkCalendar` -> `payroll_work_calendars`.
- قوانین تعطیلی: `HolidayRule` -> `payroll_holiday_rules`.
- رویداد زمانی: `TimeEvent` -> `payroll_time_events`.
- استراحت: `TimeBreak` -> `payroll_time_breaks`.
- برگه کارکرد: `Timesheet` -> `payroll_timesheets`.
- اضافه‌کاری: `OvertimeRequest` -> `payroll_overtime_requests`.
- استثنا/ناهنجاری: `AttendanceException` -> `payroll_attendance_exceptions`.
- رضایت‌نامه‌ها: `EmployeeConsent` -> `payroll_employee_consents`.
- لاگ دسترسی حساس: `SensitiveAccessLog` -> `payroll_sensitive_access_logs`.

درخواست‌ها
- نوع مرخصی: `PayrollLeaveType` -> `payroll_leave_types`.
- درخواست مرخصی: `PayrollLeaveRequest` -> `payroll_leave_requests`.
- مأموریت: `PayrollMission` -> `payroll_missions`.

حقوق و دستمزد
- دوره حقوق: `PayrollRun` -> `payroll_runs`.
- فیش: `PayrollSlip` -> `payroll_slips`.
- آیتم فیش: `PayrollItem` -> `payroll_items`.

کسورات و وام
- وام: `PayrollLoan` -> `payroll_loans`.
- اقساط وام: `PayrollLoanInstallment` -> `payroll_loan_installments`.
- مساعده: `PayrollAdvance` -> `payroll_advances`.
- تسویه: `PayrollSettlement` -> `payroll_settlements`.

جداول انطباق
- حداقل دستمزد: `PayrollMinimumWageTable` -> `payroll_minimum_wage_tables`.
- مزایا: `PayrollAllowanceTable` -> `payroll_allowance_tables`.
- بیمه: `PayrollInsuranceTable` -> `payroll_insurance_tables`.
- مالیات: `PayrollTaxTable` -> `payroll_tax_tables`.
- براکت مالیات: `PayrollTaxBracket` -> `payroll_tax_brackets`.

آدیت + وبهوک + AI
- رخداد آدیت: `PayrollAuditEvent` -> `payroll_audit_events`.
- اشتراک وبهوک: `PayrollWebhookSubscription` -> `payroll_webhook_subscriptions`.
- ارسال وبهوک: `PayrollWebhookDelivery` -> `payroll_webhook_deliveries`.
- لاگ AI: `PayrollAiLog` -> `payroll_ai_logs`.

تنانت و ایزولیشن
- همه مدل‌ها از `UsesTenant` استفاده می‌کنند: `packages/filament-payroll-attendance-ir/src/Models/Concerns/UsesTenant.php`.
- پیشوند جدول‌ها در تنظیمات: `packages/filament-payroll-attendance-ir/config/filament-payroll-attendance-ir.php`.

## نقشه جریان‌ها (Workflow)
حضور/غیاب مبتنی بر رویداد
- Use-case ها: `ClockIn`, `ClockOut` در `src/Application/UseCases`.
- کنترل حریم خصوصی: `PrivacyEnforcer` (حذف داده مکانی/بیومتریک بدون رضایت/فعال‌سازی).
- ارزیابی سیاست و ضدتقلب: `AttendancePolicyEngine` + `AntiFraudDetector`.
- استثناها در `AttendanceException` ثبت می‌شوند.

حضور/غیاب مبتنی بر پانچ
- ثبت پانچ‌ها در `PayrollTimePunch`.
- محاسبه کارکرد از روی برنامه/شیفت در `AttendanceCalculatorService::recalculateForSchedule()`.

مرخصی و درخواست‌ها
- ثبت درخواست مرخصی: `PayrollLeaveRequest`.
- تایید/رد در UI و API (`PayrollLeaveRequestResource`, `LeaveRequestController::approve`).

Payroll Run
- تولید دوره حقوق در `PayrollRunService::generate`.
- محاسبات از طریق `PayrollCalculatorService` و `AttendanceSummaryService`.
- تایید/پست/قفل در UI و API.

گزارش مدیریتی + AI
- صفحه: `AttendanceManagementReportsPage`.
- سرویس: `AiReportService` (غیرفعال پیش‌فرض، با `FakeAiProvider`).

## نقشه UI (Filament)
ثبت از طریق `FilamentPayrollAttendanceIrPlugin`:
- HR: `PayrollEmployeeResource`, `PayrollContractResource`.
- Attendance: `PayrollAttendanceShiftResource`, `PayrollAttendanceScheduleResource`, `PayrollTimePunchResource`, `PayrollAttendanceRecordResource`, `PayrollAttendanceExceptionResource`, `PayrollLeaveTypeResource`, `PayrollLeaveRequestResource`.
- Payroll: `PayrollRunResource`, `PayrollSlipResource`, `PayrollLoanResource`, `PayrollAdvanceResource`.
- تنظیمات حقوق: `PayrollMinimumWageTableResource`, `PayrollAllowanceTableResource`, `PayrollInsuranceTableResource`, `PayrollTaxTableResource`.
- گزارش مدیریتی: `AttendanceManagementReportsPage`.

## نقشه API (v1)
مسیرها: `packages/filament-payroll-attendance-ir/routes/api.php`.
- پایه: `/api/v1/payroll-attendance`.
- میدل‌ویرها: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, `throttle`.
- اسکوپ‌ها: `filamat-iam.scope:*`.

کنترلرها/منابع:
- کارکنان، قرارداد، شیفت، برنامه، پانچ، کارکرد، مرخصی، Payroll run/slip، وام، مساعده.
- OpenAPI دستی: `PayrollAttendanceOpenApi::toArray()`.

## IAM و مجوزها
- ثبت قابلیت‌ها: `packages/filament-payroll-attendance-ir/src/Support/PayrollAttendanceCapabilities.php`.
- سیاست‌ها: `FilamentPayrollAttendanceIrServiceProvider` + `Policies/*`.
- Filament: `IamResource` + `InteractsWithTenant`.

## نقاط اتصال (Integration)
- حسابداری: وابستگی به `Vendor\FilamentAccountingIr` (شرکت/شعبه) و جداول مرتبط.
- وبهوک‌ها: `PayrollWebhookService` + `SendPayrollWebhookJob`.
- سناریوی عمیق: `scripts/deep_scenario_runner.php` شامل مسیر payroll/attendance.
- تست‌ها: `tests/Feature/PayrollAttendanceRunTest.php`, `tests/Feature/HrAttendancePolicyTest.php`.
- Seed انطباق: `packages/filament-payroll-attendance-ir/database/seeders/PayrollComplianceSeeder.php`.
- اعلان‌ها: استفاده از `Filament\Notifications\Notification` (نه notify-core).

## نقاط درد و بدهی فنی (واقعی و قابل پیگیری)
1) دو پکیج موازی با رفتار متفاوت
- `filament-payroll-attendance` یک اسکلت جداست و باعث ابهام معماری و دوباره‌کاری می‌شود.

2) دو لایه مدل و سرویس هم‌پوشان
- Domain Models در `src/Domain/Models` در کنار مدل‌های عملیاتی در `src/Models` قرار دارند؛
  همچنین سرویس‌های جدید در `src/Application/*` و سرویس‌های قدیمی در `src/Services/*` هم‌زمان فعال‌اند.

3) پوشش UI/API ناقص برای مدل‌های دامنه
- منابع/کنترلر برای `AttendancePolicy`, `WorkCalendar`, `HolidayRule`, `TimeEvent`, `Timesheet`, `OvertimeRequest`, `MissionRequest`, `EmployeeConsent`, `SensitiveAccessLog`, `PayrollHoliday`, `PayrollSettlement`, `WebhookSubscription`, `AuditEvent` موجود نیست.

4) سیاست‌ها و state machine در UI/API به‌طور کامل استفاده نمی‌شوند
- `RequestStateMachine` و `ApprovalStateMachine` تعریف شده‌اند اما اغلب تاییدها در UI/API با تغییر رشته‌ای وضعیت انجام می‌شود.

5) OpenAPI سطحی
- `PayrollAttendanceOpenApi` فقط لیست مسیرها را دارد و اسکیمای درخواست/پاسخ و auth مثال ندارد.

6) ناهماهنگی با سامانه اعلان مرکزی
- اعلان‌ها با Filament Notification انجام می‌شود و با `haida/filament-notify-core` یکپارچه نیست.

7) جریان‌های رویداد-محور و پانچ-محور از هم جدا هستند
- `TimeEvent` و `PayrollTimePunch` مسیرهای جدا دارند و همگرا نشده‌اند (محاسبه کارکرد و گزارش‌گیری ترکیبی دشوار می‌شود).

## مستندات موجود
- مستندات قبلی ماژول: `docs/payroll-attendance/SPEC.md`, `docs/payroll-attendance/INSTALL.md`, `docs/payroll-attendance/API.md`.

# SPEC — filament-payroll-attendance

## معرفی
- پکیج: haida/filament-payroll-attendance
- توضیح: Payroll and attendance module for Iran (Filament v4).
- Service Provider: Haida\FilamentPayrollAttendance\FilamentPayrollAttendanceServiceProvider
- Filament Plugin: Haida\FilamentPayrollAttendance\FilamentPayrollAttendancePlugin (id: payroll-attendance)

## دامنه و قابلیت‌ها
- مدل‌ها:
- Advance.php
- AttendancePunch.php
- AttendanceRecord.php
- AttendanceSchedule.php
- AttendanceShift.php
- AuditEvent.php
- Contract.php
- Employee.php
- Holiday.php
- InsuranceTable.php
- LeaveRequest.php
- LeaveType.php
- Loan.php
- LoanInstallment.php
- PayrollItem.php
- PayrollRun.php
- PayrollSlip.php
- Settlement.php
- SettlementItem.php
- TaxBracket.php
- TaxTable.php
- WageTable.php
- منابع Filament:
- ندارد
- کنترلرها/API:
- ندارد
- Jobs/Queue:
- ندارد
- Policyها:
- ندارد

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): ندارد

## مدل داده
- Migrations:
- 2025_12_30_000001_create_payroll_ir_employee_tables.php
- 2025_12_30_000002_create_payroll_ir_contract_and_leave_tables.php
- 2025_12_30_000003_create_payroll_ir_attendance_tables.php
- 2025_12_30_000004_create_payroll_ir_payroll_tables.php
- 2025_12_30_000005_create_payroll_ir_reference_tables.php
- 2025_12_30_000006_create_payroll_ir_finance_tables.php
- 2025_12_30_000007_create_payroll_ir_audit_tables.php
- جدول‌ها:
- payroll_ir_advances
- payroll_ir_attendance_records
- payroll_ir_audit_events
- payroll_ir_contracts
- payroll_ir_employees
- payroll_ir_holidays
- payroll_ir_insurance_tables
- payroll_ir_leave_requests
- payroll_ir_leave_types
- payroll_ir_loan_installments
- payroll_ir_loans
- payroll_ir_payroll_items
- payroll_ir_payroll_runs
- payroll_ir_payroll_slips
- payroll_ir_punches
- payroll_ir_schedules
- payroll_ir_settlement_items
- payroll_ir_settlements
- payroll_ir_shifts
- payroll_ir_tax_brackets
- payroll_ir_tax_tables
- payroll_ir_wage_tables
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: ندارد
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/filament-payroll-attendance/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-payroll-attendance/config/filament-payroll-attendance.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت نشده در AdminPanelProvider
- Tenant Panel: ثبت نشده در TenantPanelProvider

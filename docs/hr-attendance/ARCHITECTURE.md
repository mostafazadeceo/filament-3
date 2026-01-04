# HR Attendance Architecture (M2)

## Overview
The active module is `packages/filament-payroll-attendance-ir`. It already contains a layered structure (Domain/Application/Infrastructure) while keeping legacy payroll/attendance models in `src/Models` for compatibility with existing UI/API. The upgrade strategy is to progressively wire UI and API to the new domain/use-cases without breaking current clients.

## Active package boundary
- Active: `Vendor\FilamentPayrollAttendanceIr` (registered in panel providers).
- Legacy skeleton: `packages/filament-payroll-attendance` (not wired to composer/panels).

## Layering (current state)
Domain
- Entities: `EmployeeProfile`, `Department`, `Position`, `AttendancePolicy`, `ShiftPattern`, `WorkCalendar`, `HolidayRule`, `TimeEvent`, `TimeBreak`, `Timesheet`, `AttendanceException`, `AuditEvent`, `LeaveRequest`, `MissionRequest`, `OvertimeRequest`.
- Enums: request/approval/status + state machines (`RequestStateMachine`, `ApprovalStateMachine`).

Application
- Use-cases: `ClockIn`, `ClockOut`, `RequestLeave`, `ApproveLeave`, `RequestMission`, `ApproveMission`, `AssignShift`, `GenerateTimesheets`, `ClosePayrollPeriod`, `RecalculateWorktime`, `RaiseException`, `ResolveException`, `ExportReports`.
- Services: `AttendancePolicyEngine`, `AntiFraudDetector`, `PrivacyEnforcer`, `AttendanceReportService`, `LeaveAccrualService`, `AiReportService`.

Infrastructure
- Capture drivers: `MobileDriver`, `WebDriver`, `KioskDriver`, `HardwareDeviceDriver`.
- Proofs: `GeoFenceProof`, `WifiSSIDProof`.
- Biometrics: `BiometricVerificationInterface` + `FaceVerificationDriver` (stub, opt-in).
- AI: `AiProviderInterface` + `FakeAiProvider`.

Legacy compatibility layer
- `src/Models/*` and `src/Services/*` continue to power existing Filament resources and API controllers until fully migrated.

## Data model (domain + legacy)
Domain tables (already present)
- Org: `payroll_departments`, `payroll_positions` (linked to `payroll_employees`).
- Policy/calendar: `payroll_attendance_policies`, `payroll_work_calendars`, `payroll_holiday_rules`.
- Time tracking: `payroll_time_events`, `payroll_time_breaks`, `payroll_timesheets`, `payroll_overtime_requests`, `payroll_attendance_exceptions`.
- Privacy: `payroll_employee_consents`, `payroll_sensitive_access_logs`.
- AI logs: `payroll_ai_logs`.

Legacy operational tables (still active)
- Attendance: `payroll_attendance_shifts`, `payroll_attendance_schedules`, `payroll_time_punches`, `payroll_attendance_records`.
- Payroll: `payroll_runs`, `payroll_slips`, `payroll_items`.
- HR: `payroll_employees`, `payroll_contracts`, `payroll_leave_types`, `payroll_leave_requests`, `payroll_missions`.

## Ingestion flows
Event-based (privacy-first)
- `ClockIn`/`ClockOut` -> `TimeEvent` with `PrivacyEnforcer` redaction + `AttendancePolicyEngine` evaluation.
- Anti-fraud signals are recorded as `AttendanceException` via `RaiseException`.

Punch-based (legacy)
- `PayrollTimePunch` + `PayrollAttendanceSchedule` -> `AttendanceCalculatorService::recalculateForSchedule()` -> `PayrollAttendanceRecord`.

Target normalization
- Time events become the canonical audit record; punches/schedules remain for operations and legacy UI.
- Timesheets are generated from approved attendance records + policy rules.

## UI (Filament)
Currently registered resources:
- HR: `PayrollEmployeeResource`, `PayrollContractResource`.
- Attendance: `PayrollAttendanceShiftResource`, `PayrollAttendanceScheduleResource`, `PayrollTimePunchResource`, `PayrollAttendanceRecordResource`, `PayrollAttendanceExceptionResource`, `PayrollLeaveTypeResource`, `PayrollLeaveRequestResource`.
- Payroll: `PayrollRunResource`, `PayrollSlipResource`, `PayrollLoanResource`, `PayrollAdvanceResource`, `PayrollMinimumWageTableResource`, `PayrollAllowanceTableResource`, `PayrollInsuranceTableResource`, `PayrollTaxTableResource`.
- Report page: `AttendanceManagementReportsPage`.

Missing UI coverage (to add)
- `AttendancePolicy`, `WorkCalendar`, `HolidayRule`, `TimeEvent`, `Timesheet`, `OvertimeRequest`, `MissionRequest`, `EmployeeConsent`, `SensitiveAccessLog`, `PayrollHoliday`, `PayrollSettlement`, webhook subscriptions, audit events.

## API surface
- Current base path: `/api/v1/payroll-attendance` with `ApiKeyAuth` + `ApiAuth` + `ResolveTenant` + IAM scopes.
- OpenAPI: `PayrollAttendanceOpenApi::toArray()` (manual, needs schema enrichment).
- Planned: add `time-events`, `timesheets`, `policies`, `calendars`, `exceptions`, and read-only audit endpoints.

## Tenancy and authorization
- All models use `UsesTenant` (global tenant scope + tenant_id autowrite).
- Policies use IAM capabilities in `PayrollAttendanceCapabilities`.
- Filament resources extend `IamResource` + `InteractsWithTenant`.

## Migration strategy (safe and incremental)
1) Keep legacy UI/API operational while introducing domain tables and use-cases.
2) Add new UI/API for domain models in parallel, with tenant-safe guards.
3) Gradually route actions (clock-in/out, approvals, exceptions) through use-cases.
4) Enforce privacy + audit requirements by default; keep opt-in for biometrics/AI.

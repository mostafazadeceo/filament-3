# API — filament-payroll-attendance-ir

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| POST | /api/v1/payroll-attendance/attendance-records/{attendance_record}/approve | payroll.attendance.approve | payroll.attendance.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/payroll-attendance/timesheets/generate | payroll.timesheet.manage | payroll.timesheet.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/payroll-attendance/timesheets/{timesheet}/approve | payroll.timesheet.approve | payroll.timesheet.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/payroll-attendance/attendance-exceptions/{attendance_exception}/resolve | payroll.exception.resolve | payroll.exception.resolve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/payroll-attendance/leave-requests/{leave_request}/approve | payroll.leave.approve | payroll.leave.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/payroll-attendance/mission-requests/{mission_request}/approve | payroll.mission.approve | payroll.mission.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/payroll-attendance/mission-requests/{mission_request}/reject | payroll.mission.approve | payroll.mission.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/payroll-attendance/overtime-requests/{overtime_request}/approve | payroll.overtime.approve | payroll.overtime.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/payroll-attendance/overtime-requests/{overtime_request}/reject | payroll.overtime.approve | payroll.overtime.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/payroll-attendance/payroll-runs/{payroll_run}/generate | payroll.run.manage | payroll.run.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/payroll-attendance/payroll-runs/{payroll_run}/approve | payroll.run.approve | payroll.run.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/payroll-attendance/payroll-runs/{payroll_run}/post | payroll.run.post | payroll.run.post | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/payroll-attendance/payroll-runs/{payroll_run}/lock | payroll.run.lock | payroll.run.lock | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/payroll-attendance/timesheet-summary | payroll.report.view | payroll.report.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/payroll-attendance/tardiness | payroll.report.view | payroll.report.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/payroll-attendance/overtime | payroll.report.view | payroll.report.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/payroll-attendance/leave-balance | payroll.report.view | payroll.report.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/payroll-attendance/coverage-gaps | payroll.report.view | payroll.report.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/payroll-attendance/attendance-summary | payroll.report.view | payroll.report.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/payroll-attendance/export | payroll.report.export | payroll.report.export | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/payroll-attendance/ai/manager | payroll.ai.use | payroll.ai.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/payroll-attendance/openapi | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.

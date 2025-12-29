# Payroll Attendance API (v1)

## احراز هویت
- API Key header: `X-Api-Key`
- Tenant header: `X-Tenant-ID`
- Bearer token (Sanctum)

## Endpoints
- `GET /api/v1/payroll-attendance/employees`
- `POST /api/v1/payroll-attendance/employees`
- `GET /api/v1/payroll-attendance/employees/{employee}`
- `PUT /api/v1/payroll-attendance/employees/{employee}`
- `DELETE /api/v1/payroll-attendance/employees/{employee}`

- `GET /api/v1/payroll-attendance/contracts`
- `POST /api/v1/payroll-attendance/contracts`
- `GET /api/v1/payroll-attendance/contracts/{contract}`
- `PUT /api/v1/payroll-attendance/contracts/{contract}`
- `DELETE /api/v1/payroll-attendance/contracts/{contract}`

- `GET /api/v1/payroll-attendance/shifts`
- `POST /api/v1/payroll-attendance/shifts`
- `GET /api/v1/payroll-attendance/shifts/{shift}`
- `PUT /api/v1/payroll-attendance/shifts/{shift}`
- `DELETE /api/v1/payroll-attendance/shifts/{shift}`

- `GET /api/v1/payroll-attendance/schedules`
- `POST /api/v1/payroll-attendance/schedules`
- `GET /api/v1/payroll-attendance/schedules/{schedule}`
- `PUT /api/v1/payroll-attendance/schedules/{schedule}`
- `DELETE /api/v1/payroll-attendance/schedules/{schedule}`

- `GET /api/v1/payroll-attendance/punches`
- `POST /api/v1/payroll-attendance/punches`
- `GET /api/v1/payroll-attendance/punches/{punch}`
- `PUT /api/v1/payroll-attendance/punches/{punch}`
- `DELETE /api/v1/payroll-attendance/punches/{punch}`

- `GET /api/v1/payroll-attendance/attendance-records`
- `POST /api/v1/payroll-attendance/attendance-records`
- `GET /api/v1/payroll-attendance/attendance-records/{attendance_record}`
- `PUT /api/v1/payroll-attendance/attendance-records/{attendance_record}`
- `DELETE /api/v1/payroll-attendance/attendance-records/{attendance_record}`
- `POST /api/v1/payroll-attendance/attendance-records/{attendance_record}/approve`

- `GET /api/v1/payroll-attendance/leave-types`
- `POST /api/v1/payroll-attendance/leave-types`
- `GET /api/v1/payroll-attendance/leave-types/{leave_type}`
- `PUT /api/v1/payroll-attendance/leave-types/{leave_type}`
- `DELETE /api/v1/payroll-attendance/leave-types/{leave_type}`

- `GET /api/v1/payroll-attendance/leave-requests`
- `POST /api/v1/payroll-attendance/leave-requests`
- `GET /api/v1/payroll-attendance/leave-requests/{leave_request}`
- `PUT /api/v1/payroll-attendance/leave-requests/{leave_request}`
- `DELETE /api/v1/payroll-attendance/leave-requests/{leave_request}`
- `POST /api/v1/payroll-attendance/leave-requests/{leave_request}/approve`

- `GET /api/v1/payroll-attendance/payroll-runs`
- `POST /api/v1/payroll-attendance/payroll-runs`
- `GET /api/v1/payroll-attendance/payroll-runs/{payroll_run}`
- `PUT /api/v1/payroll-attendance/payroll-runs/{payroll_run}`
- `DELETE /api/v1/payroll-attendance/payroll-runs/{payroll_run}`
- `POST /api/v1/payroll-attendance/payroll-runs/{payroll_run}/generate`
- `POST /api/v1/payroll-attendance/payroll-runs/{payroll_run}/approve`
- `POST /api/v1/payroll-attendance/payroll-runs/{payroll_run}/post`
- `POST /api/v1/payroll-attendance/payroll-runs/{payroll_run}/lock`

- `GET /api/v1/payroll-attendance/payroll-slips`
- `GET /api/v1/payroll-attendance/payroll-slips/{payroll_slip}`

- `GET /api/v1/payroll-attendance/loans`
- `POST /api/v1/payroll-attendance/loans`
- `GET /api/v1/payroll-attendance/loans/{loan}`
- `PUT /api/v1/payroll-attendance/loans/{loan}`
- `DELETE /api/v1/payroll-attendance/loans/{loan}`

- `GET /api/v1/payroll-attendance/advances`
- `POST /api/v1/payroll-attendance/advances`
- `GET /api/v1/payroll-attendance/advances/{advance}`
- `PUT /api/v1/payroll-attendance/advances/{advance}`
- `DELETE /api/v1/payroll-attendance/advances/{advance}`

- `GET /api/v1/payroll-attendance/openapi`

## وبهوک‌ها
- `attendance.approved`
- `leave.approved`
- `payroll.run.posted`

هدر امضای وبهوک:
- `X-Payroll-Signature` (HMAC SHA256)
- `X-Payroll-Event`

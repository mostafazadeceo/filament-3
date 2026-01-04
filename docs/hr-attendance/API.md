# HR Attendance API (v1)

## احراز هویت
- API Key header: `X-Api-Key`
- Tenant header: `X-Tenant-ID`
- Bearer token (Sanctum)
- دلیل دسترسی (اختیاری/الزامی بر اساس تنظیمات): `X-Access-Reason`

## Endpoints اصلی
### سیاست‌ها و تقویم
- `GET /api/v1/payroll-attendance/attendance-policies`
- `POST /api/v1/payroll-attendance/attendance-policies`
- `GET /api/v1/payroll-attendance/attendance-policies/{attendance_policy}`
- `PUT /api/v1/payroll-attendance/attendance-policies/{attendance_policy}`
- `DELETE /api/v1/payroll-attendance/attendance-policies/{attendance_policy}`
- `GET /api/v1/payroll-attendance/work-calendars`
- `POST /api/v1/payroll-attendance/work-calendars`
- `GET /api/v1/payroll-attendance/work-calendars/{work_calendar}`
- `PUT /api/v1/payroll-attendance/work-calendars/{work_calendar}`
- `DELETE /api/v1/payroll-attendance/work-calendars/{work_calendar}`
- `GET /api/v1/payroll-attendance/holiday-rules`
- `POST /api/v1/payroll-attendance/holiday-rules`
- `GET /api/v1/payroll-attendance/holiday-rules/{holiday_rule}`
- `PUT /api/v1/payroll-attendance/holiday-rules/{holiday_rule}`
- `DELETE /api/v1/payroll-attendance/holiday-rules/{holiday_rule}`

### رویدادها و کاربرگ‌ها
- `GET /api/v1/payroll-attendance/time-events`
- `POST /api/v1/payroll-attendance/time-events`
- `GET /api/v1/payroll-attendance/time-events/{time_event}`
- `PUT /api/v1/payroll-attendance/time-events/{time_event}`
- `DELETE /api/v1/payroll-attendance/time-events/{time_event}`
- `GET /api/v1/payroll-attendance/timesheets`
- `GET /api/v1/payroll-attendance/timesheets/{timesheet}`
- `POST /api/v1/payroll-attendance/timesheets/generate`
- `POST /api/v1/payroll-attendance/timesheets/{timesheet}/approve`

### استثناها
- `GET /api/v1/payroll-attendance/attendance-exceptions`
- `GET /api/v1/payroll-attendance/attendance-exceptions/{attendance_exception}`
- `POST /api/v1/payroll-attendance/attendance-exceptions/{attendance_exception}/resolve`

### درخواست‌ها
- `GET /api/v1/payroll-attendance/leave-requests`
- `POST /api/v1/payroll-attendance/leave-requests`
- `POST /api/v1/payroll-attendance/leave-requests/{leave_request}/approve`
- `GET /api/v1/payroll-attendance/mission-requests`
- `POST /api/v1/payroll-attendance/mission-requests`
- `POST /api/v1/payroll-attendance/mission-requests/{mission_request}/approve`
- `POST /api/v1/payroll-attendance/mission-requests/{mission_request}/reject`
- `GET /api/v1/payroll-attendance/overtime-requests`
- `POST /api/v1/payroll-attendance/overtime-requests`
- `POST /api/v1/payroll-attendance/overtime-requests/{overtime_request}/approve`
- `POST /api/v1/payroll-attendance/overtime-requests/{overtime_request}/reject`

### رضایت و ممیزی
- `GET /api/v1/payroll-attendance/employee-consents`
- `POST /api/v1/payroll-attendance/employee-consents`
- `GET /api/v1/payroll-attendance/employee-consents/{employee_consent}`
- `PUT /api/v1/payroll-attendance/employee-consents/{employee_consent}`
- `DELETE /api/v1/payroll-attendance/employee-consents/{employee_consent}`
- `GET /api/v1/payroll-attendance/sensitive-access-logs`
- `GET /api/v1/payroll-attendance/sensitive-access-logs/{sensitive_access_log}`
- `GET /api/v1/payroll-attendance/ai-logs`
- `GET /api/v1/payroll-attendance/ai-logs/{payroll_ai_log}`

## گزارش‌ها
- `GET /api/v1/payroll-attendance/reports/timesheet-summary`
- `GET /api/v1/payroll-attendance/reports/tardiness`
- `GET /api/v1/payroll-attendance/reports/overtime`
- `GET /api/v1/payroll-attendance/reports/leave-balance`
- `GET /api/v1/payroll-attendance/reports/coverage-gaps`
- `GET /api/v1/payroll-attendance/reports/attendance-summary`
- `POST /api/v1/payroll-attendance/reports/export`

## AI (اختیاری)
- `POST /api/v1/payroll-attendance/reports/ai/manager`

## نمونه درخواست‌ها

### ثبت رویداد حضور
```json
{
  "company_id": 1,
  "branch_id": 2,
  "employee_id": 104,
  "event_type": "clock_in",
  "event_at": "2025-01-10 08:05:00",
  "source": "mobile",
  "latitude": 35.6892,
  "longitude": 51.3890,
  "wifi_ssid": "Office-Wifi",
  "metadata": {
    "app_version": "1.0.0"
  }
}
```

### حل استثنا
```json
{
  "resolution_notes": "توضیح مدیر درباره اصلاح دستی"
}
```

### گزارش مدیریتی AI
```json
{
  "company_id": 1,
  "branch_id": 2,
  "period_start": "2025-01-01",
  "period_end": "2025-01-31"
}
```

### تولید کاربرگ‌ها
```json
{
  "company_id": 1,
  "branch_id": 2,
  "period_start": "2025-01-01",
  "period_end": "2025-01-31"
}
```

### ثبت رضایت‌نامه
```json
{
  "company_id": 1,
  "branch_id": 2,
  "employee_id": 104,
  "consent_type": "location_tracking",
  "is_granted": true
}
```

## OpenAPI
- `GET /api/v1/payroll-attendance/openapi`

<?php

namespace Vendor\FilamentPayrollAttendanceIr\Support;

class PayrollAttendanceOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Payroll Attendance API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/payroll-attendance/employees' => [
                    'get' => ['summary' => 'List employees'],
                    'post' => ['summary' => 'Create employee'],
                ],
                '/api/v1/payroll-attendance/employees/{employee}' => [
                    'get' => ['summary' => 'Show employee'],
                    'put' => ['summary' => 'Update employee'],
                    'delete' => ['summary' => 'Delete employee'],
                ],
                '/api/v1/payroll-attendance/contracts' => [
                    'get' => ['summary' => 'List contracts'],
                    'post' => ['summary' => 'Create contract'],
                ],
                '/api/v1/payroll-attendance/contracts/{contract}' => [
                    'get' => ['summary' => 'Show contract'],
                    'put' => ['summary' => 'Update contract'],
                    'delete' => ['summary' => 'Delete contract'],
                ],
                '/api/v1/payroll-attendance/shifts' => [
                    'get' => ['summary' => 'List shifts'],
                    'post' => ['summary' => 'Create shift'],
                ],
                '/api/v1/payroll-attendance/shifts/{shift}' => [
                    'get' => ['summary' => 'Show shift'],
                    'put' => ['summary' => 'Update shift'],
                    'delete' => ['summary' => 'Delete shift'],
                ],
                '/api/v1/payroll-attendance/schedules' => [
                    'get' => ['summary' => 'List schedules'],
                    'post' => ['summary' => 'Create schedule'],
                ],
                '/api/v1/payroll-attendance/schedules/{schedule}' => [
                    'get' => ['summary' => 'Show schedule'],
                    'put' => ['summary' => 'Update schedule'],
                    'delete' => ['summary' => 'Delete schedule'],
                ],
                '/api/v1/payroll-attendance/punches' => [
                    'get' => ['summary' => 'List punches'],
                    'post' => ['summary' => 'Create punch'],
                ],
                '/api/v1/payroll-attendance/punches/{punch}' => [
                    'get' => ['summary' => 'Show punch'],
                    'put' => ['summary' => 'Update punch'],
                    'delete' => ['summary' => 'Delete punch'],
                ],
                '/api/v1/payroll-attendance/attendance-records' => [
                    'get' => ['summary' => 'List attendance records'],
                    'post' => ['summary' => 'Create attendance record'],
                ],
                '/api/v1/payroll-attendance/attendance-records/{attendance_record}' => [
                    'get' => ['summary' => 'Show attendance record'],
                    'put' => ['summary' => 'Update attendance record'],
                    'delete' => ['summary' => 'Delete attendance record'],
                ],
                '/api/v1/payroll-attendance/attendance-records/{attendance_record}/approve' => [
                    'post' => ['summary' => 'Approve attendance record'],
                ],
                '/api/v1/payroll-attendance/attendance-policies' => [
                    'get' => ['summary' => 'List attendance policies'],
                    'post' => ['summary' => 'Create attendance policy'],
                ],
                '/api/v1/payroll-attendance/attendance-policies/{attendance_policy}' => [
                    'get' => ['summary' => 'Show attendance policy'],
                    'put' => ['summary' => 'Update attendance policy'],
                    'delete' => ['summary' => 'Delete attendance policy'],
                ],
                '/api/v1/payroll-attendance/time-events' => [
                    'get' => ['summary' => 'List time events'],
                    'post' => ['summary' => 'Create time event'],
                ],
                '/api/v1/payroll-attendance/time-events/{time_event}' => [
                    'get' => ['summary' => 'Show time event'],
                    'put' => ['summary' => 'Update time event'],
                    'delete' => ['summary' => 'Delete time event'],
                ],
                '/api/v1/payroll-attendance/timesheets' => [
                    'get' => ['summary' => 'List timesheets'],
                ],
                '/api/v1/payroll-attendance/timesheets/{timesheet}' => [
                    'get' => ['summary' => 'Show timesheet'],
                ],
                '/api/v1/payroll-attendance/timesheets/generate' => [
                    'post' => ['summary' => 'Generate timesheets'],
                ],
                '/api/v1/payroll-attendance/timesheets/{timesheet}/approve' => [
                    'post' => ['summary' => 'Approve timesheet'],
                ],
                '/api/v1/payroll-attendance/attendance-exceptions' => [
                    'get' => ['summary' => 'List attendance exceptions'],
                ],
                '/api/v1/payroll-attendance/attendance-exceptions/{attendance_exception}' => [
                    'get' => ['summary' => 'Show attendance exception'],
                ],
                '/api/v1/payroll-attendance/attendance-exceptions/{attendance_exception}/resolve' => [
                    'post' => ['summary' => 'Resolve attendance exception'],
                ],
                '/api/v1/payroll-attendance/leave-types' => [
                    'get' => ['summary' => 'List leave types'],
                    'post' => ['summary' => 'Create leave type'],
                ],
                '/api/v1/payroll-attendance/leave-types/{leave_type}' => [
                    'get' => ['summary' => 'Show leave type'],
                    'put' => ['summary' => 'Update leave type'],
                    'delete' => ['summary' => 'Delete leave type'],
                ],
                '/api/v1/payroll-attendance/leave-requests' => [
                    'get' => ['summary' => 'List leave requests'],
                    'post' => ['summary' => 'Create leave request'],
                ],
                '/api/v1/payroll-attendance/leave-requests/{leave_request}' => [
                    'get' => ['summary' => 'Show leave request'],
                    'put' => ['summary' => 'Update leave request'],
                    'delete' => ['summary' => 'Delete leave request'],
                ],
                '/api/v1/payroll-attendance/leave-requests/{leave_request}/approve' => [
                    'post' => ['summary' => 'Approve leave request'],
                ],
                '/api/v1/payroll-attendance/mission-requests' => [
                    'get' => ['summary' => 'List mission requests'],
                    'post' => ['summary' => 'Create mission request'],
                ],
                '/api/v1/payroll-attendance/mission-requests/{mission_request}' => [
                    'get' => ['summary' => 'Show mission request'],
                    'put' => ['summary' => 'Update mission request'],
                    'delete' => ['summary' => 'Delete mission request'],
                ],
                '/api/v1/payroll-attendance/mission-requests/{mission_request}/approve' => [
                    'post' => ['summary' => 'Approve mission request'],
                ],
                '/api/v1/payroll-attendance/mission-requests/{mission_request}/reject' => [
                    'post' => ['summary' => 'Reject mission request'],
                ],
                '/api/v1/payroll-attendance/overtime-requests' => [
                    'get' => ['summary' => 'List overtime requests'],
                    'post' => ['summary' => 'Create overtime request'],
                ],
                '/api/v1/payroll-attendance/overtime-requests/{overtime_request}' => [
                    'get' => ['summary' => 'Show overtime request'],
                    'put' => ['summary' => 'Update overtime request'],
                    'delete' => ['summary' => 'Delete overtime request'],
                ],
                '/api/v1/payroll-attendance/overtime-requests/{overtime_request}/approve' => [
                    'post' => ['summary' => 'Approve overtime request'],
                ],
                '/api/v1/payroll-attendance/overtime-requests/{overtime_request}/reject' => [
                    'post' => ['summary' => 'Reject overtime request'],
                ],
                '/api/v1/payroll-attendance/payroll-runs' => [
                    'get' => ['summary' => 'List payroll runs'],
                    'post' => ['summary' => 'Create payroll run'],
                ],
                '/api/v1/payroll-attendance/payroll-runs/{payroll_run}' => [
                    'get' => ['summary' => 'Show payroll run'],
                    'put' => ['summary' => 'Update payroll run'],
                    'delete' => ['summary' => 'Delete payroll run'],
                ],
                '/api/v1/payroll-attendance/payroll-runs/{payroll_run}/generate' => [
                    'post' => ['summary' => 'Generate payroll'],
                ],
                '/api/v1/payroll-attendance/payroll-runs/{payroll_run}/approve' => [
                    'post' => ['summary' => 'Approve payroll run'],
                ],
                '/api/v1/payroll-attendance/payroll-runs/{payroll_run}/post' => [
                    'post' => ['summary' => 'Post payroll run'],
                ],
                '/api/v1/payroll-attendance/payroll-runs/{payroll_run}/lock' => [
                    'post' => ['summary' => 'Lock payroll run'],
                ],
                '/api/v1/payroll-attendance/payroll-slips' => [
                    'get' => ['summary' => 'List payroll slips'],
                ],
                '/api/v1/payroll-attendance/payroll-slips/{payroll_slip}' => [
                    'get' => ['summary' => 'Show payroll slip'],
                ],
                '/api/v1/payroll-attendance/loans' => [
                    'get' => ['summary' => 'List loans'],
                    'post' => ['summary' => 'Create loan'],
                ],
                '/api/v1/payroll-attendance/loans/{loan}' => [
                    'get' => ['summary' => 'Show loan'],
                    'put' => ['summary' => 'Update loan'],
                    'delete' => ['summary' => 'Delete loan'],
                ],
                '/api/v1/payroll-attendance/advances' => [
                    'get' => ['summary' => 'List advances'],
                    'post' => ['summary' => 'Create advance'],
                ],
                '/api/v1/payroll-attendance/advances/{advance}' => [
                    'get' => ['summary' => 'Show advance'],
                    'put' => ['summary' => 'Update advance'],
                    'delete' => ['summary' => 'Delete advance'],
                ],
                '/api/v1/payroll-attendance/work-calendars' => [
                    'get' => ['summary' => 'List work calendars'],
                    'post' => ['summary' => 'Create work calendar'],
                ],
                '/api/v1/payroll-attendance/work-calendars/{work_calendar}' => [
                    'get' => ['summary' => 'Show work calendar'],
                    'put' => ['summary' => 'Update work calendar'],
                    'delete' => ['summary' => 'Delete work calendar'],
                ],
                '/api/v1/payroll-attendance/holiday-rules' => [
                    'get' => ['summary' => 'List holiday rules'],
                    'post' => ['summary' => 'Create holiday rule'],
                ],
                '/api/v1/payroll-attendance/holiday-rules/{holiday_rule}' => [
                    'get' => ['summary' => 'Show holiday rule'],
                    'put' => ['summary' => 'Update holiday rule'],
                    'delete' => ['summary' => 'Delete holiday rule'],
                ],
                '/api/v1/payroll-attendance/employee-consents' => [
                    'get' => ['summary' => 'List employee consents'],
                    'post' => ['summary' => 'Create employee consent'],
                ],
                '/api/v1/payroll-attendance/employee-consents/{employee_consent}' => [
                    'get' => ['summary' => 'Show employee consent'],
                    'put' => ['summary' => 'Update employee consent'],
                    'delete' => ['summary' => 'Delete employee consent'],
                ],
                '/api/v1/payroll-attendance/sensitive-access-logs' => [
                    'get' => ['summary' => 'List sensitive access logs'],
                ],
                '/api/v1/payroll-attendance/sensitive-access-logs/{sensitive_access_log}' => [
                    'get' => ['summary' => 'Show sensitive access log'],
                ],
                '/api/v1/payroll-attendance/ai-logs' => [
                    'get' => ['summary' => 'List AI logs'],
                ],
                '/api/v1/payroll-attendance/ai-logs/{payroll_ai_log}' => [
                    'get' => ['summary' => 'Show AI log'],
                ],
                '/api/v1/payroll-attendance/reports/timesheet-summary' => [
                    'get' => ['summary' => 'Timesheet summary report'],
                ],
                '/api/v1/payroll-attendance/reports/tardiness' => [
                    'get' => ['summary' => 'Tardiness report'],
                ],
                '/api/v1/payroll-attendance/reports/overtime' => [
                    'get' => ['summary' => 'Overtime report'],
                ],
                '/api/v1/payroll-attendance/reports/leave-balance' => [
                    'get' => ['summary' => 'Leave balance report'],
                ],
                '/api/v1/payroll-attendance/reports/coverage-gaps' => [
                    'get' => ['summary' => 'Coverage gaps report'],
                ],
                '/api/v1/payroll-attendance/reports/attendance-summary' => [
                    'get' => ['summary' => 'Attendance summary report'],
                ],
                '/api/v1/payroll-attendance/reports/export' => [
                    'post' => ['summary' => 'Export attendance reports'],
                ],
                '/api/v1/payroll-attendance/reports/ai/manager' => [
                    'post' => ['summary' => 'AI manager report'],
                ],
                '/api/v1/payroll-attendance/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}

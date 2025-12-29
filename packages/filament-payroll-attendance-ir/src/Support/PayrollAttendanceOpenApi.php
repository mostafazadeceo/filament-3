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
                '/api/v1/payroll-attendance/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}

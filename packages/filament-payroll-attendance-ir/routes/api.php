<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Illuminate\Support\Facades\Route;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\AdvanceController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\AttendanceExceptionController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\AttendancePolicyController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\AttendanceRecordController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\ContractController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\EmployeeConsentController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\EmployeeController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\HolidayRuleController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\LeaveRequestController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\LeaveTypeController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\LoanController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\MissionRequestController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\OpenApiController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\OvertimeRequestController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\PayrollAiLogController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\PayrollRunController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\PayrollSlipController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\PunchController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\ReportController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\ScheduleController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\SensitiveAccessLogController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\ShiftController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\TimeEventController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\TimesheetController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\WorkCalendarController;

Route::prefix('api/v1/payroll-attendance')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-payroll-attendance-ir.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::apiResource('employees', EmployeeController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:payroll.employee.view');
        Route::apiResource('employees', EmployeeController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:payroll.employee.manage');

        Route::apiResource('contracts', ContractController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:payroll.contract.view');
        Route::apiResource('contracts', ContractController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:payroll.contract.manage');

        Route::apiResource('shifts', ShiftController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:payroll.shift.view');
        Route::apiResource('shifts', ShiftController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:payroll.shift.manage');

        Route::apiResource('schedules', ScheduleController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:payroll.schedule.view');
        Route::apiResource('schedules', ScheduleController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:payroll.schedule.manage');

        Route::apiResource('punches', PunchController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:payroll.punch.view');
        Route::apiResource('punches', PunchController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:payroll.punch.manage');

        Route::apiResource('attendance-records', AttendanceRecordController::class)
            ->only(['index', 'show'])
            ->parameters(['attendance-records' => 'attendance_record'])
            ->middleware('filamat-iam.scope:payroll.attendance.view');
        Route::apiResource('attendance-records', AttendanceRecordController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['attendance-records' => 'attendance_record'])
            ->middleware('filamat-iam.scope:payroll.attendance.manage');
        Route::post('attendance-records/{attendance_record}/approve', [AttendanceRecordController::class, 'approve'])
            ->middleware('filamat-iam.scope:payroll.attendance.approve');

        Route::apiResource('attendance-policies', AttendancePolicyController::class)
            ->only(['index', 'show'])
            ->parameters(['attendance-policies' => 'attendance_policy'])
            ->middleware('filamat-iam.scope:payroll.policy.view');
        Route::apiResource('attendance-policies', AttendancePolicyController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['attendance-policies' => 'attendance_policy'])
            ->middleware('filamat-iam.scope:payroll.policy.manage');

        Route::apiResource('time-events', TimeEventController::class)
            ->only(['index', 'show'])
            ->parameters(['time-events' => 'time_event'])
            ->middleware('filamat-iam.scope:payroll.time_event.view');
        Route::apiResource('time-events', TimeEventController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['time-events' => 'time_event'])
            ->middleware('filamat-iam.scope:payroll.time_event.manage');

        Route::apiResource('timesheets', TimesheetController::class)
            ->only(['index', 'show'])
            ->parameters(['timesheets' => 'timesheet'])
            ->middleware('filamat-iam.scope:payroll.timesheet.view');
        Route::post('timesheets/generate', [TimesheetController::class, 'generate'])
            ->middleware('filamat-iam.scope:payroll.timesheet.manage');
        Route::post('timesheets/{timesheet}/approve', [TimesheetController::class, 'approve'])
            ->middleware('filamat-iam.scope:payroll.timesheet.approve');

        Route::apiResource('attendance-exceptions', AttendanceExceptionController::class)
            ->only(['index', 'show'])
            ->parameters(['attendance-exceptions' => 'attendance_exception'])
            ->middleware('filamat-iam.scope:payroll.exception.view');
        Route::post('attendance-exceptions/{attendance_exception}/resolve', [AttendanceExceptionController::class, 'resolve'])
            ->middleware('filamat-iam.scope:payroll.exception.resolve');

        Route::apiResource('leave-types', LeaveTypeController::class)
            ->only(['index', 'show'])
            ->parameters(['leave-types' => 'leave_type'])
            ->middleware('filamat-iam.scope:payroll.leave.view');
        Route::apiResource('leave-types', LeaveTypeController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['leave-types' => 'leave_type'])
            ->middleware('filamat-iam.scope:payroll.leave.manage');

        Route::apiResource('leave-requests', LeaveRequestController::class)
            ->only(['index', 'show'])
            ->parameters(['leave-requests' => 'leave_request'])
            ->middleware('filamat-iam.scope:payroll.leave.view');
        Route::apiResource('leave-requests', LeaveRequestController::class)
            ->only(['store'])
            ->parameters(['leave-requests' => 'leave_request'])
            ->middleware('filamat-iam.scope:payroll.leave.request');
        Route::apiResource('leave-requests', LeaveRequestController::class)
            ->only(['update', 'destroy'])
            ->parameters(['leave-requests' => 'leave_request'])
            ->middleware('filamat-iam.scope:payroll.leave.manage');
        Route::post('leave-requests/{leave_request}/approve', [LeaveRequestController::class, 'approve'])
            ->middleware('filamat-iam.scope:payroll.leave.approve');

        Route::apiResource('mission-requests', MissionRequestController::class)
            ->only(['index', 'show'])
            ->parameters(['mission-requests' => 'mission_request'])
            ->middleware('filamat-iam.scope:payroll.mission.view');
        Route::apiResource('mission-requests', MissionRequestController::class)
            ->only(['store'])
            ->parameters(['mission-requests' => 'mission_request'])
            ->middleware('filamat-iam.scope:payroll.mission.request');
        Route::apiResource('mission-requests', MissionRequestController::class)
            ->only(['update', 'destroy'])
            ->parameters(['mission-requests' => 'mission_request'])
            ->middleware('filamat-iam.scope:payroll.mission.manage');
        Route::post('mission-requests/{mission_request}/approve', [MissionRequestController::class, 'approve'])
            ->middleware('filamat-iam.scope:payroll.mission.approve');
        Route::post('mission-requests/{mission_request}/reject', [MissionRequestController::class, 'reject'])
            ->middleware('filamat-iam.scope:payroll.mission.approve');

        Route::apiResource('overtime-requests', OvertimeRequestController::class)
            ->only(['index', 'show'])
            ->parameters(['overtime-requests' => 'overtime_request'])
            ->middleware('filamat-iam.scope:payroll.overtime.view');
        Route::apiResource('overtime-requests', OvertimeRequestController::class)
            ->only(['store'])
            ->parameters(['overtime-requests' => 'overtime_request'])
            ->middleware('filamat-iam.scope:payroll.overtime.request');
        Route::apiResource('overtime-requests', OvertimeRequestController::class)
            ->only(['update', 'destroy'])
            ->parameters(['overtime-requests' => 'overtime_request'])
            ->middleware('filamat-iam.scope:payroll.overtime.manage');
        Route::post('overtime-requests/{overtime_request}/approve', [OvertimeRequestController::class, 'approve'])
            ->middleware('filamat-iam.scope:payroll.overtime.approve');
        Route::post('overtime-requests/{overtime_request}/reject', [OvertimeRequestController::class, 'reject'])
            ->middleware('filamat-iam.scope:payroll.overtime.approve');

        Route::apiResource('payroll-runs', PayrollRunController::class)
            ->only(['index', 'show'])
            ->parameters(['payroll-runs' => 'payroll_run'])
            ->middleware('filamat-iam.scope:payroll.run.view');
        Route::apiResource('payroll-runs', PayrollRunController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['payroll-runs' => 'payroll_run'])
            ->middleware('filamat-iam.scope:payroll.run.manage');
        Route::post('payroll-runs/{payroll_run}/generate', [PayrollRunController::class, 'generate'])
            ->middleware('filamat-iam.scope:payroll.run.manage');
        Route::post('payroll-runs/{payroll_run}/approve', [PayrollRunController::class, 'approve'])
            ->middleware('filamat-iam.scope:payroll.run.approve');
        Route::post('payroll-runs/{payroll_run}/post', [PayrollRunController::class, 'post'])
            ->middleware('filamat-iam.scope:payroll.run.post');
        Route::post('payroll-runs/{payroll_run}/lock', [PayrollRunController::class, 'lock'])
            ->middleware('filamat-iam.scope:payroll.run.lock');

        Route::apiResource('payroll-slips', PayrollSlipController::class)
            ->only(['index', 'show'])
            ->parameters(['payroll-slips' => 'payroll_slip'])
            ->middleware('filamat-iam.scope:payroll.slip.view');

        Route::apiResource('loans', LoanController::class)
            ->only(['index', 'show', 'store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:payroll.loan.manage');

        Route::apiResource('advances', AdvanceController::class)
            ->only(['index', 'show', 'store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:payroll.advance.manage');

        Route::apiResource('work-calendars', WorkCalendarController::class)
            ->only(['index', 'show'])
            ->parameters(['work-calendars' => 'work_calendar'])
            ->middleware('filamat-iam.scope:payroll.calendar.view');
        Route::apiResource('work-calendars', WorkCalendarController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['work-calendars' => 'work_calendar'])
            ->middleware('filamat-iam.scope:payroll.calendar.manage');

        Route::apiResource('holiday-rules', HolidayRuleController::class)
            ->only(['index', 'show'])
            ->parameters(['holiday-rules' => 'holiday_rule'])
            ->middleware('filamat-iam.scope:payroll.calendar.view');
        Route::apiResource('holiday-rules', HolidayRuleController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['holiday-rules' => 'holiday_rule'])
            ->middleware('filamat-iam.scope:payroll.calendar.manage');

        Route::apiResource('employee-consents', EmployeeConsentController::class)
            ->only(['index', 'show'])
            ->parameters(['employee-consents' => 'employee_consent'])
            ->middleware('filamat-iam.scope:payroll.consent.view');
        Route::apiResource('employee-consents', EmployeeConsentController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['employee-consents' => 'employee_consent'])
            ->middleware('filamat-iam.scope:payroll.consent.manage');

        Route::apiResource('sensitive-access-logs', SensitiveAccessLogController::class)
            ->only(['index', 'show'])
            ->parameters(['sensitive-access-logs' => 'sensitive_access_log'])
            ->middleware('filamat-iam.scope:payroll.audit.view');

        Route::apiResource('ai-logs', PayrollAiLogController::class)
            ->only(['index', 'show'])
            ->parameters(['ai-logs' => 'payroll_ai_log'])
            ->middleware('filamat-iam.scope:payroll.ai.view');

        Route::prefix('reports')->group(function (): void {
            Route::get('timesheet-summary', [ReportController::class, 'timesheetSummary'])
                ->middleware('filamat-iam.scope:payroll.report.view');
            Route::get('tardiness', [ReportController::class, 'tardiness'])
                ->middleware('filamat-iam.scope:payroll.report.view');
            Route::get('overtime', [ReportController::class, 'overtime'])
                ->middleware('filamat-iam.scope:payroll.report.view');
            Route::get('leave-balance', [ReportController::class, 'leaveBalance'])
                ->middleware('filamat-iam.scope:payroll.report.view');
            Route::get('coverage-gaps', [ReportController::class, 'coverageGaps'])
                ->middleware('filamat-iam.scope:payroll.report.view');
            Route::get('attendance-summary', [ReportController::class, 'attendanceSummary'])
                ->middleware('filamat-iam.scope:payroll.report.view');
            Route::post('export', [ReportController::class, 'export'])
                ->middleware('filamat-iam.scope:payroll.report.export');
            Route::post('ai/manager', [ReportController::class, 'managerReport'])
                ->middleware('filamat-iam.scope:payroll.ai.use');
        });

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:payroll.view');
    });

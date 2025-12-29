<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Illuminate\Support\Facades\Route;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\AdvanceController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\AttendanceRecordController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\ContractController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\EmployeeController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\LeaveRequestController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\LeaveTypeController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\LoanController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\OpenApiController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\PayrollRunController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\PayrollSlipController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\PunchController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\ScheduleController;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\ShiftController;

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

        Route::get('openapi', [OpenApiController::class, 'show']);
    });

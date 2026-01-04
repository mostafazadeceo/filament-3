<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceRecord;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceSchedule;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLeaveRequest;

class AttendanceReportService
{
    /**
     * @return Collection<int, \stdClass>
     */
    public function timesheetSummary(int $companyId, ?int $branchId, Carbon $start, Carbon $end): Collection
    {
        return PayrollAttendanceRecord::query()
            ->select([
                'employee_id',
                DB::raw('SUM(worked_minutes) as worked_minutes'),
                DB::raw('SUM(overtime_minutes) as overtime_minutes'),
                DB::raw('SUM(night_minutes) as night_minutes'),
                DB::raw('SUM(friday_minutes) as friday_minutes'),
                DB::raw('SUM(holiday_minutes) as holiday_minutes'),
                DB::raw('SUM(late_minutes) as late_minutes'),
                DB::raw('SUM(early_leave_minutes) as early_leave_minutes'),
                DB::raw('SUM(absence_minutes) as absence_minutes'),
            ])
            ->where('company_id', $companyId)
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('status', ['approved', 'locked'])
            ->groupBy('employee_id')
            ->get();
    }

    /**
     * @return Collection<int, \stdClass>
     */
    public function tardinessReport(int $companyId, ?int $branchId, Carbon $start, Carbon $end): Collection
    {
        return PayrollAttendanceRecord::query()
            ->select([
                'employee_id',
                DB::raw('SUM(late_minutes) as late_minutes'),
                DB::raw('SUM(early_leave_minutes) as early_leave_minutes'),
            ])
            ->where('company_id', $companyId)
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('status', ['approved', 'locked'])
            ->groupBy('employee_id')
            ->get();
    }

    /**
     * @return Collection<int, \stdClass>
     */
    public function overtimeReport(int $companyId, ?int $branchId, Carbon $start, Carbon $end): Collection
    {
        return PayrollAttendanceRecord::query()
            ->select([
                'employee_id',
                DB::raw('SUM(overtime_minutes) as overtime_minutes'),
            ])
            ->where('company_id', $companyId)
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('status', ['approved', 'locked'])
            ->groupBy('employee_id')
            ->get();
    }

    /**
     * @return Collection<int, \stdClass>
     */
    public function leaveBalanceReport(int $companyId, ?int $branchId, Carbon $start, Carbon $end): Collection
    {
        return PayrollLeaveRequest::query()
            ->select([
                'employee_id',
                'leave_type_id',
                DB::raw('SUM(duration_hours) as used_hours'),
            ])
            ->where('company_id', $companyId)
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->whereBetween('start_date', [$start->toDateString(), $end->toDateString()])
            ->where('status', 'approved')
            ->groupBy('employee_id', 'leave_type_id')
            ->get();
    }

    /**
     * @return Collection<int, \stdClass>
     */
    public function coverageGapReport(int $companyId, ?int $branchId, Carbon $start, Carbon $end): Collection
    {
        $scheduled = PayrollAttendanceSchedule::query()
            ->select([
                'work_date',
                DB::raw('COUNT(*) as scheduled_count'),
            ])
            ->where('company_id', $companyId)
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('work_date');

        return PayrollAttendanceRecord::query()
            ->select([
                'payroll_attendance_records.work_date',
                DB::raw('COUNT(payroll_attendance_records.id) as attended_count'),
                DB::raw('scheduled.scheduled_count as scheduled_count'),
                DB::raw('(scheduled.scheduled_count - COUNT(payroll_attendance_records.id)) as gap_count'),
            ])
            ->joinSub($scheduled, 'scheduled', function ($join): void {
                $join->on('scheduled.work_date', '=', 'payroll_attendance_records.work_date');
            })
            ->where('payroll_attendance_records.company_id', $companyId)
            ->when($branchId, fn ($query) => $query->where('payroll_attendance_records.branch_id', $branchId))
            ->whereBetween('payroll_attendance_records.work_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('payroll_attendance_records.status', ['approved', 'locked'])
            ->groupBy('payroll_attendance_records.work_date', 'scheduled.scheduled_count')
            ->get();
    }
}

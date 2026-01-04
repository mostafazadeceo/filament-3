<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\UseCases;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Vendor\FilamentPayrollAttendanceIr\Application\Services\AttendancePolicyResolver;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\TimesheetPeriodType;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\TimesheetStatus;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\Timesheet;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;
use Vendor\FilamentPayrollAttendanceIr\Services\AttendanceSummaryService;

class GenerateTimesheets
{
    public function __construct(
        private readonly AttendanceSummaryService $summaryService,
        private readonly AttendancePolicyResolver $policyResolver,
        private readonly RaiseException $raiseException,
    ) {}

    /**
     * @return Collection<int, Timesheet>
     */
    public function execute(int $companyId, ?int $branchId, Carbon $start, Carbon $end): Collection
    {
        $employees = PayrollEmployee::query()
            ->where('company_id', $companyId)
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->get();

        $timesheets = new Collection;

        foreach ($employees as $employee) {
            $summary = $this->summaryService->summarize($employee->getKey(), $start, $end);
            $this->checkOvertimeCap($companyId, $branchId, $employee->getKey(), $summary);

            $timesheets->push(Timesheet::query()->updateOrCreate(
                [
                    'employee_id' => $employee->getKey(),
                    'period_start' => $start->toDateString(),
                    'period_end' => $end->toDateString(),
                    'period_type' => TimesheetPeriodType::Monthly->value,
                ],
                [
                    'company_id' => $companyId,
                    'branch_id' => $branchId,
                    'status' => TimesheetStatus::Draft->value,
                    'worked_minutes' => $summary['worked_minutes'] ?? 0,
                    'overtime_minutes' => $summary['overtime_minutes'] ?? 0,
                    'night_minutes' => $summary['night_minutes'] ?? 0,
                    'friday_minutes' => $summary['friday_minutes'] ?? 0,
                    'holiday_minutes' => $summary['holiday_minutes'] ?? 0,
                    'late_minutes' => $summary['late_minutes'] ?? 0,
                    'early_leave_minutes' => $summary['early_leave_minutes'] ?? 0,
                    'absence_minutes' => $summary['absence_minutes'] ?? 0,
                ]
            ));
        }

        return $timesheets;
    }

    /**
     * @param  array<string, int>  $summary
     */
    private function checkOvertimeCap(int $companyId, ?int $branchId, int $employeeId, array $summary): void
    {
        $policy = $this->policyResolver->resolve($companyId, $branchId);
        if (! $policy) {
            return;
        }

        $rules = array_merge(
            (array) config('filament-payroll-attendance-ir.policy.default_rules', []),
            $policy->rules ?? []
        );

        $cap = $rules['max_overtime_minutes'] ?? null;
        if (! $cap) {
            return;
        }

        if (($summary['overtime_minutes'] ?? 0) > (int) $cap) {
            $this->raiseException->execute([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'employee_id' => $employeeId,
                'type' => 'overtime_cap_exceeded',
                'severity' => 'medium',
                'detected_at' => now(),
                'metadata' => [
                    'cap_minutes' => (int) $cap,
                    'actual_minutes' => (int) ($summary['overtime_minutes'] ?? 0),
                ],
                'assigned_to' => $rules['exception_assignee_id'] ?? null,
            ]);
        }
    }
}

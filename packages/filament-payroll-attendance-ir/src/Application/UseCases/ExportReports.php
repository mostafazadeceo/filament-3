<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\UseCases;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Vendor\FilamentPayrollAttendanceIr\Application\Services\AttendanceReportService;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\Timesheet;
use Vendor\FilamentPayrollAttendanceIr\Services\AttendanceSummaryService;

class ExportReports
{
    public function __construct(
        private readonly AttendanceSummaryService $summaryService,
        private readonly AttendanceReportService $reportService,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function execute(string $type, array $filters = []): array
    {
        return match ($type) {
            'timesheets' => [
                'type' => 'timesheets',
                'data' => $this->exportTimesheets($filters),
            ],
            'timesheet_summary' => [
                'type' => 'timesheet_summary',
                'data' => $this->exportTimesheetSummary($filters),
            ],
            'tardiness' => [
                'type' => 'tardiness',
                'data' => $this->exportTardinessReport($filters),
            ],
            'overtime' => [
                'type' => 'overtime',
                'data' => $this->exportOvertimeReport($filters),
            ],
            'leave_balance' => [
                'type' => 'leave_balance',
                'data' => $this->exportLeaveBalanceReport($filters),
            ],
            'coverage_gaps' => [
                'type' => 'coverage_gaps',
                'data' => $this->exportCoverageGapReport($filters),
            ],
            'attendance_summary' => [
                'type' => 'attendance_summary',
                'data' => $this->exportAttendanceSummary($filters),
            ],
            default => [
                'type' => $type,
                'data' => [],
            ],
        };
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, Timesheet>
     */
    protected function exportTimesheets(array $filters): Collection
    {
        return Timesheet::query()
            ->when(isset($filters['company_id']), fn ($q) => $q->where('company_id', $filters['company_id']))
            ->when(isset($filters['branch_id']), fn ($q) => $q->where('branch_id', $filters['branch_id']))
            ->when(isset($filters['employee_id']), fn ($q) => $q->where('employee_id', $filters['employee_id']))
            ->when(isset($filters['period_start']), fn ($q) => $q->whereDate('period_start', $filters['period_start']))
            ->when(isset($filters['period_end']), fn ($q) => $q->whereDate('period_end', $filters['period_end']))
            ->get();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, int>
     */
    protected function exportAttendanceSummary(array $filters): array
    {
        $employeeId = (int) ($filters['employee_id'] ?? 0);
        if (! $employeeId) {
            return [];
        }

        $start = isset($filters['start'])
            ? Carbon::parse($filters['start'])
            : now()->startOfMonth();
        $end = isset($filters['end'])
            ? Carbon::parse($filters['end'])
            : now()->endOfMonth();

        return $this->summaryService->summarize($employeeId, $start, $end);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, \stdClass>
     */
    protected function exportTimesheetSummary(array $filters): Collection
    {
        [$companyId, $branchId, $start, $end] = $this->resolveContext($filters);

        if (! $companyId) {
            return collect();
        }

        return $this->reportService->timesheetSummary($companyId, $branchId, $start, $end);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, \stdClass>
     */
    protected function exportTardinessReport(array $filters): Collection
    {
        [$companyId, $branchId, $start, $end] = $this->resolveContext($filters);

        if (! $companyId) {
            return collect();
        }

        return $this->reportService->tardinessReport($companyId, $branchId, $start, $end);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, \stdClass>
     */
    protected function exportOvertimeReport(array $filters): Collection
    {
        [$companyId, $branchId, $start, $end] = $this->resolveContext($filters);

        if (! $companyId) {
            return collect();
        }

        return $this->reportService->overtimeReport($companyId, $branchId, $start, $end);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, \stdClass>
     */
    protected function exportLeaveBalanceReport(array $filters): Collection
    {
        [$companyId, $branchId, $start, $end] = $this->resolveContext($filters);

        if (! $companyId) {
            return collect();
        }

        return $this->reportService->leaveBalanceReport($companyId, $branchId, $start, $end);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, \stdClass>
     */
    protected function exportCoverageGapReport(array $filters): Collection
    {
        [$companyId, $branchId, $start, $end] = $this->resolveContext($filters);

        if (! $companyId) {
            return collect();
        }

        return $this->reportService->coverageGapReport($companyId, $branchId, $start, $end);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{int, int|null, Carbon, Carbon}
     */
    protected function resolveContext(array $filters): array
    {
        $companyId = (int) ($filters['company_id'] ?? 0);
        $branchId = isset($filters['branch_id']) ? (int) $filters['branch_id'] : null;
        $start = isset($filters['start']) ? Carbon::parse($filters['start']) : now()->startOfMonth();
        $end = isset($filters['end']) ? Carbon::parse($filters['end']) : now()->endOfMonth();

        return [$companyId, $branchId, $start, $end];
    }
}

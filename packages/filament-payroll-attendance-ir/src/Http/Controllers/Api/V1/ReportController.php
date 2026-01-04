<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Carbon\Carbon;
use Vendor\FilamentPayrollAttendanceIr\Application\Services\AiReportService;
use Vendor\FilamentPayrollAttendanceIr\Application\Services\AttendanceReportService;
use Vendor\FilamentPayrollAttendanceIr\Application\UseCases\ExportReports;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\AiManagerReportRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\AttendanceSummaryRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\ReportExportRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\ReportFiltersRequest;
use Vendor\FilamentPayrollAttendanceIr\Services\AttendanceSummaryService;

class ReportController extends ApiController
{
    public function timesheetSummary(ReportFiltersRequest $request): array
    {
        [$companyId, $branchId, $start, $end] = $this->resolveContext($request->validated());

        $data = app(AttendanceReportService::class)->timesheetSummary($companyId, $branchId, $start, $end);

        return ['data' => $data];
    }

    public function tardiness(ReportFiltersRequest $request): array
    {
        [$companyId, $branchId, $start, $end] = $this->resolveContext($request->validated());

        $data = app(AttendanceReportService::class)->tardinessReport($companyId, $branchId, $start, $end);

        return ['data' => $data];
    }

    public function overtime(ReportFiltersRequest $request): array
    {
        [$companyId, $branchId, $start, $end] = $this->resolveContext($request->validated());

        $data = app(AttendanceReportService::class)->overtimeReport($companyId, $branchId, $start, $end);

        return ['data' => $data];
    }

    public function leaveBalance(ReportFiltersRequest $request): array
    {
        [$companyId, $branchId, $start, $end] = $this->resolveContext($request->validated());

        $data = app(AttendanceReportService::class)->leaveBalanceReport($companyId, $branchId, $start, $end);

        return ['data' => $data];
    }

    public function coverageGaps(ReportFiltersRequest $request): array
    {
        [$companyId, $branchId, $start, $end] = $this->resolveContext($request->validated());

        $data = app(AttendanceReportService::class)->coverageGapReport($companyId, $branchId, $start, $end);

        return ['data' => $data];
    }

    public function attendanceSummary(AttendanceSummaryRequest $request): array
    {
        $payload = $request->validated();
        $start = isset($payload['start']) ? Carbon::parse($payload['start']) : now()->startOfMonth();
        $end = isset($payload['end']) ? Carbon::parse($payload['end']) : now()->endOfMonth();

        $summary = app(AttendanceSummaryService::class)->summarize((int) $payload['employee_id'], $start, $end);

        return ['data' => $summary];
    }

    public function export(ReportExportRequest $request): array
    {
        $payload = $request->validated();
        $filters = (array) ($payload['filters'] ?? []);

        if (! isset($filters['period_start']) && isset($filters['start'])) {
            $filters['period_start'] = $filters['start'];
        }
        if (! isset($filters['period_end']) && isset($filters['end'])) {
            $filters['period_end'] = $filters['end'];
        }
        if (! isset($filters['start']) && isset($filters['period_start'])) {
            $filters['start'] = $filters['period_start'];
        }
        if (! isset($filters['end']) && isset($filters['period_end'])) {
            $filters['end'] = $filters['period_end'];
        }

        return app(ExportReports::class)->execute($payload['type'], $filters);
    }

    public function managerReport(AiManagerReportRequest $request): array
    {
        $payload = $request->validated();
        $payload['period_start'] = $payload['period_start'] ?? now()->startOfMonth()->toDateString();
        $payload['period_end'] = $payload['period_end'] ?? now()->endOfMonth()->toDateString();

        return app(AiReportService::class)->generatePersianManagerReport($payload);
    }

    /**
     * @param  array{company_id:int, branch_id?:int|null, start?:string|null, end?:string|null}  $payload
     * @return array{int, int|null, Carbon, Carbon}
     */
    private function resolveContext(array $payload): array
    {
        $companyId = (int) ($payload['company_id'] ?? 0);
        $branchId = isset($payload['branch_id']) ? (int) $payload['branch_id'] : null;
        $start = isset($payload['start']) ? Carbon::parse($payload['start']) : now()->startOfMonth();
        $end = isset($payload['end']) ? Carbon::parse($payload['end']) : now()->endOfMonth();

        return [$companyId, $branchId, $start, $end];
    }
}

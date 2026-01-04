<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Application\UseCases\GenerateTimesheets;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\TimesheetStatus;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\Timesheet;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\GenerateTimesheetsRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\TimesheetResource;

class TimesheetController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(Timesheet::class, 'timesheet');
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Timesheet::query()->latest('period_start');

        if ($request->filled('company_id')) {
            $query->where('company_id', (int) $request->input('company_id'));
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', (int) $request->input('branch_id'));
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', (int) $request->input('employee_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        if ($request->filled('period_start')) {
            $query->whereDate('period_start', '>=', (string) $request->input('period_start'));
        }

        if ($request->filled('period_end')) {
            $query->whereDate('period_end', '<=', (string) $request->input('period_end'));
        }

        return TimesheetResource::collection($query->paginate());
    }

    public function show(Timesheet $timesheet): TimesheetResource
    {
        $this->logSensitiveAccess($timesheet);

        return new TimesheetResource($timesheet);
    }

    public function approve(Timesheet $timesheet): TimesheetResource
    {
        $this->authorize('approve', $timesheet);

        $timesheet->update([
            'status' => TimesheetStatus::Approved->value,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return new TimesheetResource($timesheet->refresh());
    }

    public function generate(GenerateTimesheetsRequest $request): AnonymousResourceCollection
    {
        $payload = $request->validated();

        $branchId = array_key_exists('branch_id', $payload) && $payload['branch_id'] !== null
            ? (int) $payload['branch_id']
            : null;

        $timesheets = app(GenerateTimesheets::class)->execute(
            (int) $payload['company_id'],
            $branchId,
            Carbon::parse($payload['period_start']),
            Carbon::parse($payload['period_end'])
        );

        return TimesheetResource::collection($timesheets);
    }
}

<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreLeaveRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateLeaveRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\LeaveRequestResource;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLeaveRequest;
use Vendor\FilamentPayrollAttendanceIr\Services\PayrollWebhookService;

class LeaveRequestController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PayrollLeaveRequest::class, 'leave_request');
    }

    public function index(): AnonymousResourceCollection
    {
        $requests = PayrollLeaveRequest::query()->latest()->paginate();

        return LeaveRequestResource::collection($requests);
    }

    public function show(PayrollLeaveRequest $leave_request): LeaveRequestResource
    {
        return new LeaveRequestResource($leave_request);
    }

    public function store(StoreLeaveRequest $request): LeaveRequestResource
    {
        $leaveRequest = PayrollLeaveRequest::query()->create($request->validated());

        return new LeaveRequestResource($leaveRequest);
    }

    public function update(UpdateLeaveRequest $request, PayrollLeaveRequest $leave_request): LeaveRequestResource
    {
        $leave_request->update($request->validated());

        return new LeaveRequestResource($leave_request->refresh());
    }

    public function destroy(PayrollLeaveRequest $leave_request): array
    {
        $leave_request->delete();

        return ['status' => 'ok'];
    }

    public function approve(PayrollLeaveRequest $leave_request): LeaveRequestResource
    {
        $this->authorize('approve', $leave_request);

        $leave_request->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        app(PayrollWebhookService::class)->dispatch('leave.approved', $leave_request->company_id, [
            'id' => $leave_request->getKey(),
            'employee_id' => $leave_request->employee_id,
            'start_date' => $leave_request->start_date,
            'end_date' => $leave_request->end_date,
            'status' => $leave_request->status,
        ]);

        return new LeaveRequestResource($leave_request->refresh());
    }
}

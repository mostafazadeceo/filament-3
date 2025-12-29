<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreLeaveTypeRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateLeaveTypeRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\LeaveTypeResource;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLeaveType;

class LeaveTypeController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PayrollLeaveType::class, 'leave_type');
    }

    public function index(): AnonymousResourceCollection
    {
        $types = PayrollLeaveType::query()->latest()->paginate();

        return LeaveTypeResource::collection($types);
    }

    public function show(PayrollLeaveType $leave_type): LeaveTypeResource
    {
        return new LeaveTypeResource($leave_type);
    }

    public function store(StoreLeaveTypeRequest $request): LeaveTypeResource
    {
        $type = PayrollLeaveType::query()->create($request->validated());

        return new LeaveTypeResource($type);
    }

    public function update(UpdateLeaveTypeRequest $request, PayrollLeaveType $leave_type): LeaveTypeResource
    {
        $leave_type->update($request->validated());

        return new LeaveTypeResource($leave_type->refresh());
    }

    public function destroy(PayrollLeaveType $leave_type): array
    {
        $leave_type->delete();

        return ['status' => 'ok'];
    }
}

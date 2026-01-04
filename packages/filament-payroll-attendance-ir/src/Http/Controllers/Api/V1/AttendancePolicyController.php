<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendancePolicy;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreAttendancePolicyRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateAttendancePolicyRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\AttendancePolicyResource;

class AttendancePolicyController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(AttendancePolicy::class, 'attendance_policy');
    }

    public function index(): AnonymousResourceCollection
    {
        $policies = AttendancePolicy::query()->latest()->paginate();

        return AttendancePolicyResource::collection($policies);
    }

    public function show(AttendancePolicy $attendance_policy): AttendancePolicyResource
    {
        return new AttendancePolicyResource($attendance_policy);
    }

    public function store(StoreAttendancePolicyRequest $request): AttendancePolicyResource
    {
        $policy = AttendancePolicy::query()->create($request->validated());

        return new AttendancePolicyResource($policy);
    }

    public function update(UpdateAttendancePolicyRequest $request, AttendancePolicy $attendance_policy): AttendancePolicyResource
    {
        $attendance_policy->update($request->validated());

        return new AttendancePolicyResource($attendance_policy->refresh());
    }

    public function destroy(AttendancePolicy $attendance_policy): array
    {
        $attendance_policy->delete();

        return ['status' => 'ok'];
    }
}

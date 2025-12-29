<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreShiftRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateShiftRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\ShiftResource;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceShift;

class ShiftController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PayrollAttendanceShift::class, 'shift');
    }

    public function index(): AnonymousResourceCollection
    {
        $shifts = PayrollAttendanceShift::query()->latest()->paginate();

        return ShiftResource::collection($shifts);
    }

    public function show(PayrollAttendanceShift $shift): ShiftResource
    {
        return new ShiftResource($shift);
    }

    public function store(StoreShiftRequest $request): ShiftResource
    {
        $shift = PayrollAttendanceShift::query()->create($request->validated());

        return new ShiftResource($shift);
    }

    public function update(UpdateShiftRequest $request, PayrollAttendanceShift $shift): ShiftResource
    {
        $shift->update($request->validated());

        return new ShiftResource($shift->refresh());
    }

    public function destroy(PayrollAttendanceShift $shift): array
    {
        $shift->delete();

        return ['status' => 'ok'];
    }
}

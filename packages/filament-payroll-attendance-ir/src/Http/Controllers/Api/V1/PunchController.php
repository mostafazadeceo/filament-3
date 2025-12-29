<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StorePunchRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdatePunchRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\PunchResource;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTimePunch;

class PunchController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PayrollTimePunch::class, 'punch');
    }

    public function index(): AnonymousResourceCollection
    {
        $punches = PayrollTimePunch::query()->latest()->paginate();

        return PunchResource::collection($punches);
    }

    public function show(PayrollTimePunch $punch): PunchResource
    {
        return new PunchResource($punch);
    }

    public function store(StorePunchRequest $request): PunchResource
    {
        $punch = PayrollTimePunch::query()->create($request->validated());

        return new PunchResource($punch);
    }

    public function update(UpdatePunchRequest $request, PayrollTimePunch $punch): PunchResource
    {
        $punch->update($request->validated());

        return new PunchResource($punch->refresh());
    }

    public function destroy(PayrollTimePunch $punch): array
    {
        $punch->delete();

        return ['status' => 'ok'];
    }
}

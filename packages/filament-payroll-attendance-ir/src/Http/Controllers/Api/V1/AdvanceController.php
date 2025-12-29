<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreAdvanceRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateAdvanceRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\AdvanceResource;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAdvance;

class AdvanceController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PayrollAdvance::class, 'advance');
    }

    public function index(): AnonymousResourceCollection
    {
        $advances = PayrollAdvance::query()->latest()->paginate();

        return AdvanceResource::collection($advances);
    }

    public function show(PayrollAdvance $advance): AdvanceResource
    {
        return new AdvanceResource($advance);
    }

    public function store(StoreAdvanceRequest $request): AdvanceResource
    {
        $advance = PayrollAdvance::query()->create($request->validated());

        return new AdvanceResource($advance);
    }

    public function update(UpdateAdvanceRequest $request, PayrollAdvance $advance): AdvanceResource
    {
        $advance->update($request->validated());

        return new AdvanceResource($advance->refresh());
    }

    public function destroy(PayrollAdvance $advance): array
    {
        $advance->delete();

        return ['status' => 'ok'];
    }
}

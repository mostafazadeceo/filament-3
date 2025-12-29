<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreContractRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateContractRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\ContractResource;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollContract;

class ContractController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PayrollContract::class, 'contract');
    }

    public function index(): AnonymousResourceCollection
    {
        $contracts = PayrollContract::query()->latest()->paginate();

        return ContractResource::collection($contracts);
    }

    public function show(PayrollContract $contract): ContractResource
    {
        return new ContractResource($contract);
    }

    public function store(StoreContractRequest $request): ContractResource
    {
        $contract = PayrollContract::query()->create($request->validated());

        return new ContractResource($contract);
    }

    public function update(UpdateContractRequest $request, PayrollContract $contract): ContractResource
    {
        $contract->update($request->validated());

        return new ContractResource($contract->refresh());
    }

    public function destroy(PayrollContract $contract): array
    {
        $contract->delete();

        return ['status' => 'ok'];
    }
}

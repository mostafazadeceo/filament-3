<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreContractRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateContractRequest;
use Vendor\FilamentAccountingIr\Http\Resources\ContractResource;
use Vendor\FilamentAccountingIr\Models\Contract;

class ContractController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $contracts = Contract::query()->latest()->paginate();

        return ContractResource::collection($contracts);
    }

    public function show(Contract $contract): ContractResource
    {
        return new ContractResource($contract);
    }

    public function store(StoreContractRequest $request): ContractResource
    {
        $contract = Contract::query()->create($request->validated());

        return new ContractResource($contract);
    }

    public function update(UpdateContractRequest $request, Contract $contract): ContractResource
    {
        $contract->update($request->validated());

        return new ContractResource($contract);
    }

    public function destroy(Contract $contract): array
    {
        $contract->delete();

        return ['status' => 'ok'];
    }
}

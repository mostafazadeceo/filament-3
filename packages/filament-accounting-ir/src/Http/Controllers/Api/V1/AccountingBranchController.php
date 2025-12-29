<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreAccountingBranchRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateAccountingBranchRequest;
use Vendor\FilamentAccountingIr\Http\Resources\AccountingBranchResource;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;

class AccountingBranchController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $branches = AccountingBranch::query()->latest()->paginate();

        return AccountingBranchResource::collection($branches);
    }

    public function show(AccountingBranch $branch): AccountingBranchResource
    {
        return new AccountingBranchResource($branch);
    }

    public function store(StoreAccountingBranchRequest $request): AccountingBranchResource
    {
        $branch = AccountingBranch::query()->create($request->validated());

        return new AccountingBranchResource($branch);
    }

    public function update(UpdateAccountingBranchRequest $request, AccountingBranch $branch): AccountingBranchResource
    {
        $branch->update($request->validated());

        return new AccountingBranchResource($branch);
    }

    public function destroy(AccountingBranch $branch): array
    {
        $branch->delete();

        return ['status' => 'ok'];
    }
}

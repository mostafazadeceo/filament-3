<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreAccountPlanRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateAccountPlanRequest;
use Vendor\FilamentAccountingIr\Http\Resources\AccountPlanResource;
use Vendor\FilamentAccountingIr\Models\AccountPlan;

class AccountPlanController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $plans = AccountPlan::query()->latest()->paginate();

        return AccountPlanResource::collection($plans);
    }

    public function show(AccountPlan $account_plan): AccountPlanResource
    {
        return new AccountPlanResource($account_plan);
    }

    public function store(StoreAccountPlanRequest $request): AccountPlanResource
    {
        $plan = AccountPlan::query()->create($request->validated());

        return new AccountPlanResource($plan);
    }

    public function update(UpdateAccountPlanRequest $request, AccountPlan $account_plan): AccountPlanResource
    {
        $account_plan->update($request->validated());

        return new AccountPlanResource($account_plan);
    }

    public function destroy(AccountPlan $account_plan): array
    {
        $account_plan->delete();

        return ['status' => 'ok'];
    }
}

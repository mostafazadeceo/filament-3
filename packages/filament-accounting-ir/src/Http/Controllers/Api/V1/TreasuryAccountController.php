<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreTreasuryAccountRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateTreasuryAccountRequest;
use Vendor\FilamentAccountingIr\Http\Resources\TreasuryAccountResource;
use Vendor\FilamentAccountingIr\Models\TreasuryAccount;

class TreasuryAccountController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $accounts = TreasuryAccount::query()->latest()->paginate();

        return TreasuryAccountResource::collection($accounts);
    }

    public function show(TreasuryAccount $treasury_account): TreasuryAccountResource
    {
        return new TreasuryAccountResource($treasury_account);
    }

    public function store(StoreTreasuryAccountRequest $request): TreasuryAccountResource
    {
        $account = TreasuryAccount::query()->create($request->validated());

        return new TreasuryAccountResource($account);
    }

    public function update(UpdateTreasuryAccountRequest $request, TreasuryAccount $treasury_account): TreasuryAccountResource
    {
        $treasury_account->update($request->validated());

        return new TreasuryAccountResource($treasury_account);
    }

    public function destroy(TreasuryAccount $treasury_account): array
    {
        $treasury_account->delete();

        return ['status' => 'ok'];
    }
}

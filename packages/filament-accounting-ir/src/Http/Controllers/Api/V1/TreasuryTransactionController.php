<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Events\TreasuryTransactionPosted;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreTreasuryTransactionRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateTreasuryTransactionRequest;
use Vendor\FilamentAccountingIr\Http\Resources\TreasuryTransactionResource;
use Vendor\FilamentAccountingIr\Models\TreasuryTransaction;

class TreasuryTransactionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = TreasuryTransaction::query()->latest('transaction_date')->paginate();

        return TreasuryTransactionResource::collection($items);
    }

    public function show(TreasuryTransaction $treasury_transaction): TreasuryTransactionResource
    {
        return new TreasuryTransactionResource($treasury_transaction);
    }

    public function store(StoreTreasuryTransactionRequest $request): TreasuryTransactionResource
    {
        $item = TreasuryTransaction::query()->create($request->validated());

        event(new TreasuryTransactionPosted($item));

        return new TreasuryTransactionResource($item);
    }

    public function update(UpdateTreasuryTransactionRequest $request, TreasuryTransaction $treasury_transaction): TreasuryTransactionResource
    {
        $treasury_transaction->update($request->validated());

        return new TreasuryTransactionResource($treasury_transaction);
    }

    public function destroy(TreasuryTransaction $treasury_transaction): array
    {
        $treasury_transaction->delete();

        return ['status' => 'ok'];
    }
}

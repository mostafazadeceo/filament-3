<?php

namespace Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1;

use Haida\FilamentLoyaltyClub\Http\Requests\StoreCustomerRequest;
use Haida\FilamentLoyaltyClub\Http\Requests\UpdateCustomerRequest;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyPointsBucket;
use Haida\FilamentLoyaltyClub\Services\LoyaltyLedgerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $customers = LoyaltyCustomer::query()
            ->with(['tier', 'walletAccount'])
            ->paginate(20);

        return response()->json(['data' => $customers]);
    }

    public function show(int $customer): JsonResponse
    {
        $record = LoyaltyCustomer::query()
            ->with(['tier', 'walletAccount'])
            ->findOrFail($customer);

        return response()->json(['data' => $record]);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $data = $request->validated();
        $record = LoyaltyCustomer::query()->create($data);

        return response()->json(['data' => $record], 201);
    }

    public function update(UpdateCustomerRequest $request, int $customer): JsonResponse
    {
        $record = LoyaltyCustomer::query()->findOrFail($customer);
        $record->update($request->validated());

        return response()->json(['data' => $record]);
    }

    public function balances(int $customer, LoyaltyLedgerService $ledgerService): JsonResponse
    {
        $record = LoyaltyCustomer::query()->findOrFail($customer);
        $account = $ledgerService->getOrCreateAccount($record);

        $expiryDays = (array) config('filament-loyalty-club.points.expiry.notify_days_before', [30]);
        $maxDays = $expiryDays ? max($expiryDays) : 30;
        $expiringPoints = LoyaltyPointsBucket::query()
            ->where('tenant_id', $record->tenant_id)
            ->where('customer_id', $record->getKey())
            ->where('points_available', '>', 0)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays((int) $maxDays))
            ->sum('points_available');

        return response()->json([
            'data' => [
                'points_balance' => $account->points_balance,
                'cashback_balance' => $account->cashback_balance,
                'expiring_points' => (int) $expiringPoints,
            ],
        ]);
    }
}

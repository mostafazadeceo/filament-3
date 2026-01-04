<?php

namespace Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1;

use Haida\FilamentLoyaltyClub\Http\Requests\RedeemRewardRequest;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReward;
use Haida\FilamentLoyaltyClub\Services\LoyaltyRewardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RewardController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $rewards = LoyaltyReward::query()
            ->where('status', 'active')
            ->paginate(20);

        return response()->json(['data' => $rewards]);
    }

    public function redeem(int $reward, RedeemRewardRequest $request, LoyaltyRewardService $service): JsonResponse
    {
        $reward = LoyaltyReward::query()->findOrFail($reward);
        $customer = LoyaltyCustomer::query()->findOrFail((int) $request->validated()['customer_id']);

        $redemption = $service->redeemReward($customer, $reward, $request->validated()['payload'] ?? []);

        return response()->json(['data' => $redemption], 201);
    }
}

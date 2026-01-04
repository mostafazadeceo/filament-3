<?php

namespace Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1;

use Haida\FilamentLoyaltyClub\Http\Requests\RedeemCouponRequest;
use Haida\FilamentLoyaltyClub\Http\Requests\ValidateCouponRequest;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Services\LoyaltyCouponService;
use Illuminate\Http\JsonResponse;

class CouponController extends ApiController
{
    public function validateCoupon(ValidateCouponRequest $request, LoyaltyCouponService $service): JsonResponse
    {
        $data = $request->validated();
        $customer = LoyaltyCustomer::query()->findOrFail((int) $data['customer_id']);

        $coupon = $service->validateCoupon($customer, (string) $data['code'], $data);

        return response()->json(['data' => $coupon]);
    }

    public function redeem(RedeemCouponRequest $request, LoyaltyCouponService $service): JsonResponse
    {
        $data = $request->validated();
        $customer = LoyaltyCustomer::query()->findOrFail((int) $data['customer_id']);

        $redemption = $service->redeemCoupon($customer, (string) $data['code'], $data);

        return response()->json(['data' => $redemption], 201);
    }
}

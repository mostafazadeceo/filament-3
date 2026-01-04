<?php

namespace Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1;

use Haida\FilamentLoyaltyClub\Http\Requests\StoreReferralRequest;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReferral;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReferralProgram;
use Haida\FilamentLoyaltyClub\Services\LoyaltyReferralService;
use Illuminate\Http\JsonResponse;

class ReferralController extends ApiController
{
    public function store(StoreReferralRequest $request, LoyaltyReferralService $service): JsonResponse
    {
        $data = $request->validated();
        $program = LoyaltyReferralProgram::query()->findOrFail((int) $data['program_id']);
        $referrer = LoyaltyCustomer::query()->findOrFail((int) $data['referrer_customer_id']);

        $referral = $service->createReferral($program, $referrer, $data);

        return response()->json(['data' => $referral], 201);
    }

    public function show(int $referral): JsonResponse
    {
        $record = LoyaltyReferral::query()->findOrFail($referral);

        return response()->json(['data' => $record]);
    }
}

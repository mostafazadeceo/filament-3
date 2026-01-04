<?php

namespace Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1;

use Haida\FilamentLoyaltyClub\Http\Requests\OffersRequest;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Services\LoyaltyCampaignService;
use Illuminate\Http\JsonResponse;

class CampaignController extends ApiController
{
    public function offers(OffersRequest $request, LoyaltyCampaignService $service): JsonResponse
    {
        $data = $request->validated();
        $customer = LoyaltyCustomer::query()->findOrFail((int) $data['customer_id']);
        $offers = $service->getOffersForCustomer($customer);

        return response()->json(['data' => $offers]);
    }
}

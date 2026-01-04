<?php

namespace Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1;

use Haida\FilamentLoyaltyClub\Http\Requests\StoreEventRequest;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Services\LoyaltyEventService;
use Illuminate\Http\JsonResponse;

class EventController extends ApiController
{
    public function store(StoreEventRequest $request, LoyaltyEventService $service): JsonResponse
    {
        $data = $request->validated();
        $customer = LoyaltyCustomer::query()->findOrFail((int) $data['customer_id']);

        $event = $service->ingest(
            $customer,
            (string) $data['type'],
            $data['payload'] ?? [],
            (string) $data['idempotency_key'],
            $data['source'] ?? null,
        );

        return response()->json(['data' => $event], 201);
    }
}

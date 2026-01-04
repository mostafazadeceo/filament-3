<?php

namespace Haida\FilamentCommerceExperience\Http\Controllers\Api\V1;

use Haida\FilamentCommerceExperience\Http\Requests\StoreBuyNowRequest;
use Haida\FilamentCommerceExperience\Services\BuyNowService;
use Illuminate\Http\JsonResponse;

class BuyNowController
{
    public function store(StoreBuyNowRequest $request, BuyNowService $service): JsonResponse
    {
        $preference = $service->enable($request->validated(), $request->ip());

        return response()->json([
            'id' => $preference->getKey(),
            'status' => $preference->status,
        ]);
    }
}

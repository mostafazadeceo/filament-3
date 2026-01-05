<?php

namespace Haida\FilamentCommerceExperience\Http\Controllers\Api\V1;

use Haida\FilamentCommerceExperience\Http\Requests\StoreCsatSurveyRequest;
use Haida\FilamentCommerceExperience\Services\CsatSurveyService;
use Illuminate\Http\JsonResponse;

class CsatController
{
    public function store(StoreCsatSurveyRequest $request, CsatSurveyService $service): JsonResponse
    {
        $survey = $service->createSurvey($request->validated());

        return response()->json([
            'id' => $survey->getKey(),
            'status' => $survey->status,
        ]);
    }
}

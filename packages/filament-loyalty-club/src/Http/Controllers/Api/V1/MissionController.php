<?php

namespace Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1;

use Haida\FilamentLoyaltyClub\Models\LoyaltyMission;
use Haida\FilamentLoyaltyClub\Models\LoyaltyMissionProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MissionController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $customerId = $request->query('customer_id');
        $missions = LoyaltyMission::query()
            ->where('status', 'active')
            ->get();

        if ($customerId) {
            $progress = LoyaltyMissionProgress::query()
                ->where('customer_id', (int) $customerId)
                ->get()
                ->keyBy('mission_id');

            $missions->transform(function (LoyaltyMission $mission) use ($progress) {
                $mission->setAttribute('progress', $progress->get($mission->getKey()));

                return $mission;
            });
        }

        return response()->json(['data' => $missions]);
    }

    public function progress(int $mission, Request $request): JsonResponse
    {
        $customerId = (int) $request->query('customer_id');
        $progress = LoyaltyMissionProgress::query()
            ->where('mission_id', $mission)
            ->where('customer_id', $customerId)
            ->first();

        return response()->json(['data' => $progress]);
    }
}

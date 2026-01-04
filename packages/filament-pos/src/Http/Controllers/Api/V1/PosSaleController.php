<?php

namespace Haida\FilamentPos\Http\Controllers\Api\V1;

use Haida\FilamentPos\Http\Requests\StorePosSaleRequest;
use Haida\FilamentPos\Http\Resources\PosSaleResource as PosSaleApiResource;
use Haida\FilamentPos\Models\PosCashierSession;
use Haida\FilamentPos\Services\PosSaleService;

class PosSaleController extends ApiController
{
    public function store(StorePosSaleRequest $request, PosSaleService $service): PosSaleApiResource
    {
        $data = $request->validated();
        $items = $data['items'] ?? [];
        $payments = $data['payments'] ?? [];
        unset($data['items'], $data['payments']);

        $session = null;
        if (! empty($data['session_id'])) {
            $session = PosCashierSession::query()->find($data['session_id']);
        }

        $sale = $service->createSale($data, $items, $payments, $session, auth()->user(), false);

        return new PosSaleApiResource($sale);
    }
}

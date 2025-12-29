<?php

namespace Haida\FilamentRestaurantOps\Http\Controllers\Api\V1;

use Haida\FilamentRestaurantOps\Http\Requests\StorePurchaseRequest;
use Haida\FilamentRestaurantOps\Http\Requests\UpdatePurchaseRequest;
use Haida\FilamentRestaurantOps\Http\Resources\PurchaseRequestResource;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(RestaurantPurchaseRequest::class, 'purchase_request');
    }

    public function index(): AnonymousResourceCollection
    {
        $requests = RestaurantPurchaseRequest::query()
            ->with('lines')
            ->latest('created_at')
            ->paginate();

        return PurchaseRequestResource::collection($requests);
    }

    public function show(RestaurantPurchaseRequest $purchase_request): PurchaseRequestResource
    {
        $purchase_request->load('lines');

        return new PurchaseRequestResource($purchase_request);
    }

    public function store(StorePurchaseRequest $request): PurchaseRequestResource
    {
        $data = $request->validated();
        $lines = $data['lines'] ?? [];
        unset($data['lines']);

        $purchaseRequest = DB::transaction(function () use ($data, $lines): RestaurantPurchaseRequest {
            $purchaseRequest = RestaurantPurchaseRequest::query()->create($data);
            $this->syncLines($purchaseRequest, $lines);

            return $purchaseRequest->refresh();
        });

        return new PurchaseRequestResource($purchaseRequest->load('lines'));
    }

    public function update(UpdatePurchaseRequest $request, RestaurantPurchaseRequest $purchase_request): PurchaseRequestResource
    {
        $data = $request->validated();
        $lines = $data['lines'] ?? null;
        unset($data['lines']);

        $purchaseRequest = DB::transaction(function () use ($purchase_request, $data, $lines): RestaurantPurchaseRequest {
            $purchase_request->update($data);
            if (is_array($lines)) {
                $this->syncLines($purchase_request, $lines);
            }

            return $purchase_request->refresh();
        });

        return new PurchaseRequestResource($purchaseRequest->load('lines'));
    }

    public function destroy(RestaurantPurchaseRequest $purchase_request): array
    {
        $purchase_request->delete();

        return ['status' => 'ok'];
    }

    protected function syncLines(RestaurantPurchaseRequest $purchaseRequest, array $lines): void
    {
        $purchaseRequest->lines()->delete();

        foreach ($lines as $line) {
            $purchaseRequest->lines()->create([
                'item_id' => $line['item_id'] ?? null,
                'uom_id' => $line['uom_id'] ?? null,
                'quantity' => $line['quantity'] ?? 0,
                'notes' => $line['notes'] ?? null,
            ]);
        }
    }
}

<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreInventoryItemRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateInventoryItemRequest;
use Vendor\FilamentAccountingIr\Http\Resources\InventoryItemResource;
use Vendor\FilamentAccountingIr\Models\InventoryItem;

class InventoryItemController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = InventoryItem::query()->latest()->paginate();

        return InventoryItemResource::collection($items);
    }

    public function show(InventoryItem $inventory_item): InventoryItemResource
    {
        return new InventoryItemResource($inventory_item);
    }

    public function store(StoreInventoryItemRequest $request): InventoryItemResource
    {
        $item = InventoryItem::query()->create($request->validated());

        return new InventoryItemResource($item);
    }

    public function update(UpdateInventoryItemRequest $request, InventoryItem $inventory_item): InventoryItemResource
    {
        $inventory_item->update($request->validated());

        return new InventoryItemResource($inventory_item);
    }

    public function destroy(InventoryItem $inventory_item): array
    {
        $inventory_item->delete();

        return ['status' => 'ok'];
    }
}

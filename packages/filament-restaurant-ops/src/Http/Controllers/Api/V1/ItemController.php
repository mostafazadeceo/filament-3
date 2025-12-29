<?php

namespace Haida\FilamentRestaurantOps\Http\Controllers\Api\V1;

use Haida\FilamentRestaurantOps\Http\Requests\StoreItemRequest;
use Haida\FilamentRestaurantOps\Http\Requests\UpdateItemRequest;
use Haida\FilamentRestaurantOps\Http\Resources\ItemResource;
use Haida\FilamentRestaurantOps\Models\RestaurantItem;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ItemController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(RestaurantItem::class, 'item');
    }

    public function index(): AnonymousResourceCollection
    {
        $items = RestaurantItem::query()->latest()->paginate();

        return ItemResource::collection($items);
    }

    public function show(RestaurantItem $item): ItemResource
    {
        return new ItemResource($item);
    }

    public function store(StoreItemRequest $request): ItemResource
    {
        $item = RestaurantItem::query()->create($request->validated());

        return new ItemResource($item);
    }

    public function update(UpdateItemRequest $request, RestaurantItem $item): ItemResource
    {
        $item->update($request->validated());

        return new ItemResource($item->refresh());
    }

    public function destroy(RestaurantItem $item): array
    {
        $item->delete();

        return ['status' => 'ok'];
    }
}

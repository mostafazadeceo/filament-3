<?php

namespace Haida\FilamentRestaurantOps\Http\Controllers\Api\V1;

use Haida\FilamentRestaurantOps\Http\Requests\StoreWarehouseRequest;
use Haida\FilamentRestaurantOps\Http\Requests\UpdateWarehouseRequest;
use Haida\FilamentRestaurantOps\Http\Resources\WarehouseResource;
use Haida\FilamentRestaurantOps\Models\RestaurantWarehouse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WarehouseController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(RestaurantWarehouse::class, 'warehouse');
    }

    public function index(): AnonymousResourceCollection
    {
        $warehouses = RestaurantWarehouse::query()->latest()->paginate();

        return WarehouseResource::collection($warehouses);
    }

    public function show(RestaurantWarehouse $warehouse): WarehouseResource
    {
        return new WarehouseResource($warehouse);
    }

    public function store(StoreWarehouseRequest $request): WarehouseResource
    {
        $warehouse = RestaurantWarehouse::query()->create($request->validated());

        return new WarehouseResource($warehouse);
    }

    public function update(UpdateWarehouseRequest $request, RestaurantWarehouse $warehouse): WarehouseResource
    {
        $warehouse->update($request->validated());

        return new WarehouseResource($warehouse->refresh());
    }

    public function destroy(RestaurantWarehouse $warehouse): array
    {
        $warehouse->delete();

        return ['status' => 'ok'];
    }
}

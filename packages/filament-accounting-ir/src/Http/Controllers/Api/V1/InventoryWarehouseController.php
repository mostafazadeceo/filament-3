<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreInventoryWarehouseRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateInventoryWarehouseRequest;
use Vendor\FilamentAccountingIr\Http\Resources\InventoryWarehouseResource;
use Vendor\FilamentAccountingIr\Models\InventoryWarehouse;

class InventoryWarehouseController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $warehouses = InventoryWarehouse::query()->latest()->paginate();

        return InventoryWarehouseResource::collection($warehouses);
    }

    public function show(InventoryWarehouse $inventory_warehouse): InventoryWarehouseResource
    {
        return new InventoryWarehouseResource($inventory_warehouse);
    }

    public function store(StoreInventoryWarehouseRequest $request): InventoryWarehouseResource
    {
        $warehouse = InventoryWarehouse::query()->create($request->validated());

        return new InventoryWarehouseResource($warehouse);
    }

    public function update(UpdateInventoryWarehouseRequest $request, InventoryWarehouse $inventory_warehouse): InventoryWarehouseResource
    {
        $inventory_warehouse->update($request->validated());

        return new InventoryWarehouseResource($inventory_warehouse);
    }

    public function destroy(InventoryWarehouse $inventory_warehouse): array
    {
        $inventory_warehouse->delete();

        return ['status' => 'ok'];
    }
}

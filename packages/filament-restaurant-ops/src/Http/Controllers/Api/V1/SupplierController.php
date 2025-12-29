<?php

namespace Haida\FilamentRestaurantOps\Http\Controllers\Api\V1;

use Haida\FilamentRestaurantOps\Http\Requests\StoreSupplierRequest;
use Haida\FilamentRestaurantOps\Http\Requests\UpdateSupplierRequest;
use Haida\FilamentRestaurantOps\Http\Resources\SupplierResource;
use Haida\FilamentRestaurantOps\Models\RestaurantSupplier;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SupplierController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(RestaurantSupplier::class, 'supplier');
    }

    public function index(): AnonymousResourceCollection
    {
        $suppliers = RestaurantSupplier::query()->latest()->paginate();

        return SupplierResource::collection($suppliers);
    }

    public function show(RestaurantSupplier $supplier): SupplierResource
    {
        return new SupplierResource($supplier);
    }

    public function store(StoreSupplierRequest $request): SupplierResource
    {
        $supplier = RestaurantSupplier::query()->create($request->validated());

        return new SupplierResource($supplier);
    }

    public function update(UpdateSupplierRequest $request, RestaurantSupplier $supplier): SupplierResource
    {
        $supplier->update($request->validated());

        return new SupplierResource($supplier->refresh());
    }

    public function destroy(RestaurantSupplier $supplier): array
    {
        $supplier->delete();

        return ['status' => 'ok'];
    }
}

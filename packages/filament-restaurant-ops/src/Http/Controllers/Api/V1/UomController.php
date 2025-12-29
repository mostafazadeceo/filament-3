<?php

namespace Haida\FilamentRestaurantOps\Http\Controllers\Api\V1;

use Haida\FilamentRestaurantOps\Http\Requests\StoreUomRequest;
use Haida\FilamentRestaurantOps\Http\Requests\UpdateUomRequest;
use Haida\FilamentRestaurantOps\Http\Resources\UomResource;
use Haida\FilamentRestaurantOps\Models\RestaurantUom;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UomController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(RestaurantUom::class, 'uom');
    }

    public function index(): AnonymousResourceCollection
    {
        $uoms = RestaurantUom::query()->latest()->paginate();

        return UomResource::collection($uoms);
    }

    public function show(RestaurantUom $uom): UomResource
    {
        return new UomResource($uom);
    }

    public function store(StoreUomRequest $request): UomResource
    {
        $uom = RestaurantUom::query()->create($request->validated());

        return new UomResource($uom);
    }

    public function update(UpdateUomRequest $request, RestaurantUom $uom): UomResource
    {
        $uom->update($request->validated());

        return new UomResource($uom->refresh());
    }

    public function destroy(RestaurantUom $uom): array
    {
        $uom->delete();

        return ['status' => 'ok'];
    }
}

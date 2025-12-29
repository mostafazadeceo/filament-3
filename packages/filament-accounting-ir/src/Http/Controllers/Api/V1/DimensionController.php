<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreDimensionRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateDimensionRequest;
use Vendor\FilamentAccountingIr\Http\Resources\DimensionResource;
use Vendor\FilamentAccountingIr\Models\Dimension;

class DimensionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $dimensions = Dimension::query()->latest()->paginate();

        return DimensionResource::collection($dimensions);
    }

    public function show(Dimension $dimension): DimensionResource
    {
        $dimension->load('values');

        return new DimensionResource($dimension);
    }

    public function store(StoreDimensionRequest $request): DimensionResource
    {
        $dimension = Dimension::query()->create($request->validated());

        return new DimensionResource($dimension);
    }

    public function update(UpdateDimensionRequest $request, Dimension $dimension): DimensionResource
    {
        $dimension->update($request->validated());

        return new DimensionResource($dimension);
    }

    public function destroy(Dimension $dimension): array
    {
        $dimension->delete();

        return ['status' => 'ok'];
    }
}

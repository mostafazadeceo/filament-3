<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreUomRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateUomRequest;
use Vendor\FilamentAccountingIr\Http\Resources\UomResource;
use Vendor\FilamentAccountingIr\Models\Uom;

class UomController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = Uom::query()->latest()->paginate();

        return UomResource::collection($items);
    }

    public function show(Uom $uom): UomResource
    {
        return new UomResource($uom);
    }

    public function store(StoreUomRequest $request): UomResource
    {
        $item = Uom::query()->create($request->validated());

        return new UomResource($item);
    }

    public function update(UpdateUomRequest $request, Uom $uom): UomResource
    {
        $uom->update($request->validated());

        return new UomResource($uom);
    }

    public function destroy(Uom $uom): array
    {
        $uom->delete();

        return ['status' => 'ok'];
    }
}

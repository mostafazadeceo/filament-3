<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreWithholdingRateRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateWithholdingRateRequest;
use Vendor\FilamentAccountingIr\Http\Resources\WithholdingRateResource;
use Vendor\FilamentAccountingIr\Models\WithholdingRate;

class WithholdingRateController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = WithholdingRate::query()->latest()->paginate();

        return WithholdingRateResource::collection($items);
    }

    public function show(WithholdingRate $withholding_rate): WithholdingRateResource
    {
        return new WithholdingRateResource($withholding_rate);
    }

    public function store(StoreWithholdingRateRequest $request): WithholdingRateResource
    {
        $item = WithholdingRate::query()->create($request->validated());

        return new WithholdingRateResource($item);
    }

    public function update(UpdateWithholdingRateRequest $request, WithholdingRate $withholding_rate): WithholdingRateResource
    {
        $withholding_rate->update($request->validated());

        return new WithholdingRateResource($withholding_rate);
    }

    public function destroy(WithholdingRate $withholding_rate): array
    {
        $withholding_rate->delete();

        return ['status' => 'ok'];
    }
}

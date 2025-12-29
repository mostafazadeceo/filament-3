<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreTaxRateRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateTaxRateRequest;
use Vendor\FilamentAccountingIr\Http\Resources\TaxRateResource;
use Vendor\FilamentAccountingIr\Models\TaxRate;

class TaxRateController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = TaxRate::query()->with('versions')->latest()->paginate();

        return TaxRateResource::collection($items);
    }

    public function show(TaxRate $tax_rate): TaxRateResource
    {
        $tax_rate->load('versions');

        return new TaxRateResource($tax_rate);
    }

    public function store(StoreTaxRateRequest $request): TaxRateResource
    {
        $item = TaxRate::query()->create($request->validated());

        return new TaxRateResource($item->load('versions'));
    }

    public function update(UpdateTaxRateRequest $request, TaxRate $tax_rate): TaxRateResource
    {
        $tax_rate->update($request->validated());

        return new TaxRateResource($tax_rate->load('versions'));
    }

    public function destroy(TaxRate $tax_rate): array
    {
        $tax_rate->delete();

        return ['status' => 'ok'];
    }
}

<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreTaxCategoryRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateTaxCategoryRequest;
use Vendor\FilamentAccountingIr\Http\Resources\TaxCategoryResource;
use Vendor\FilamentAccountingIr\Models\TaxCategory;

class TaxCategoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = TaxCategory::query()->latest()->paginate();

        return TaxCategoryResource::collection($items);
    }

    public function show(TaxCategory $tax_category): TaxCategoryResource
    {
        return new TaxCategoryResource($tax_category);
    }

    public function store(StoreTaxCategoryRequest $request): TaxCategoryResource
    {
        $item = TaxCategory::query()->create($request->validated());

        return new TaxCategoryResource($item);
    }

    public function update(UpdateTaxCategoryRequest $request, TaxCategory $tax_category): TaxCategoryResource
    {
        $tax_category->update($request->validated());

        return new TaxCategoryResource($tax_category);
    }

    public function destroy(TaxCategory $tax_category): array
    {
        $tax_category->delete();

        return ['status' => 'ok'];
    }
}

<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreProductServiceRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateProductServiceRequest;
use Vendor\FilamentAccountingIr\Http\Resources\ProductServiceResource;
use Vendor\FilamentAccountingIr\Models\ProductService;

class ProductServiceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = ProductService::query()->latest()->paginate();

        return ProductServiceResource::collection($items);
    }

    public function show(ProductService $product_service): ProductServiceResource
    {
        return new ProductServiceResource($product_service);
    }

    public function store(StoreProductServiceRequest $request): ProductServiceResource
    {
        $item = ProductService::query()->create($request->validated());

        return new ProductServiceResource($item);
    }

    public function update(UpdateProductServiceRequest $request, ProductService $product_service): ProductServiceResource
    {
        $product_service->update($request->validated());

        return new ProductServiceResource($product_service);
    }

    public function destroy(ProductService $product_service): array
    {
        $product_service->delete();

        return ['status' => 'ok'];
    }
}

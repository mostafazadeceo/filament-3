<?php

namespace Haida\CommerceCatalog\Http\Controllers\Api\V1;

use Haida\CommerceCatalog\Http\Requests\StoreProductRequest;
use Haida\CommerceCatalog\Http\Requests\UpdateProductRequest;
use Haida\CommerceCatalog\Http\Resources\ProductResource;
use Haida\CommerceCatalog\Models\CatalogProduct;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends ApiController
{
    public function __construct()
    {
        // API keys are used for server-to-server sync (no user session). For those requests
        // we rely on `filamat-iam.scope:*` middleware instead of per-user policies.
        $apiKeyHeader = (string) config('filamat-iam.api.api_key_header', 'X-Api-Key');
        if (! request()->header($apiKeyHeader)) {
            $this->authorizeResource(CatalogProduct::class, 'product');
        }
    }

    public function index(): AnonymousResourceCollection
    {
        $products = CatalogProduct::query()
            ->with('collections')
            ->latest()
            ->paginate();

        return ProductResource::collection($products);
    }

    public function show(CatalogProduct $product): ProductResource
    {
        return new ProductResource($product->loadMissing('collections'));
    }

    public function store(StoreProductRequest $request): ProductResource
    {
        $data = $request->validated();
        $data['currency'] = $data['currency'] ?? config('commerce-catalog.defaults.currency', 'IRR');
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        $product = CatalogProduct::query()->create($data);

        if (array_key_exists('collections', $data)) {
            $product->collections()->sync($data['collections']);
        }

        return new ProductResource($product->loadMissing('collections'));
    }

    public function update(UpdateProductRequest $request, CatalogProduct $product): ProductResource
    {
        $data = $request->validated();
        $data['updated_by_user_id'] = auth()->id();

        $product->update($data);

        if (array_key_exists('collections', $data)) {
            $product->collections()->sync($data['collections']);
        }

        return new ProductResource($product->refresh()->loadMissing('collections'));
    }

    public function destroy(CatalogProduct $product): array
    {
        $product->delete();

        return ['status' => 'ok'];
    }
}

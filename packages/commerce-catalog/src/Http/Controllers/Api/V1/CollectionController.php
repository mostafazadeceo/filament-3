<?php

namespace Haida\CommerceCatalog\Http\Controllers\Api\V1;

use Haida\CommerceCatalog\Http\Requests\StoreCollectionRequest;
use Haida\CommerceCatalog\Http\Requests\UpdateCollectionRequest;
use Haida\CommerceCatalog\Http\Resources\CollectionResource;
use Haida\CommerceCatalog\Models\CatalogCollection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CollectionController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(CatalogCollection::class, 'collection');
    }

    public function index(): AnonymousResourceCollection
    {
        $collections = CatalogCollection::query()
            ->with('products')
            ->latest()
            ->paginate();

        return CollectionResource::collection($collections);
    }

    public function show(CatalogCollection $collection): CollectionResource
    {
        return new CollectionResource($collection->loadMissing('products'));
    }

    public function store(StoreCollectionRequest $request): CollectionResource
    {
        $data = $request->validated();
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        $collection = CatalogCollection::query()->create($data);

        if (array_key_exists('products', $data)) {
            $collection->products()->sync($data['products']);
        }

        return new CollectionResource($collection->loadMissing('products'));
    }

    public function update(UpdateCollectionRequest $request, CatalogCollection $collection): CollectionResource
    {
        $data = $request->validated();
        $data['updated_by_user_id'] = auth()->id();

        $collection->update($data);

        if (array_key_exists('products', $data)) {
            $collection->products()->sync($data['products']);
        }

        return new CollectionResource($collection->refresh()->loadMissing('products'));
    }

    public function destroy(CatalogCollection $collection): array
    {
        $collection->delete();

        return ['status' => 'ok'];
    }
}

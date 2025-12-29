<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreKeyMaterialRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateKeyMaterialRequest;
use Vendor\FilamentAccountingIr\Http\Resources\KeyMaterialResource;
use Vendor\FilamentAccountingIr\Models\KeyMaterial;

class KeyMaterialController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = KeyMaterial::query()->latest()->paginate();

        return KeyMaterialResource::collection($items);
    }

    public function show(KeyMaterial $key_material): KeyMaterialResource
    {
        return new KeyMaterialResource($key_material);
    }

    public function store(StoreKeyMaterialRequest $request): KeyMaterialResource
    {
        $item = KeyMaterial::query()->create($request->validated());

        return new KeyMaterialResource($item);
    }

    public function update(UpdateKeyMaterialRequest $request, KeyMaterial $key_material): KeyMaterialResource
    {
        $key_material->update($request->validated());

        return new KeyMaterialResource($key_material);
    }

    public function destroy(KeyMaterial $key_material): array
    {
        $key_material->delete();

        return ['status' => 'ok'];
    }
}

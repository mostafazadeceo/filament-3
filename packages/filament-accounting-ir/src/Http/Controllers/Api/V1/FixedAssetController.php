<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreFixedAssetRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateFixedAssetRequest;
use Vendor\FilamentAccountingIr\Http\Resources\FixedAssetResource;
use Vendor\FilamentAccountingIr\Models\FixedAsset;

class FixedAssetController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $assets = FixedAsset::query()->latest()->paginate();

        return FixedAssetResource::collection($assets);
    }

    public function show(FixedAsset $fixed_asset): FixedAssetResource
    {
        return new FixedAssetResource($fixed_asset);
    }

    public function store(StoreFixedAssetRequest $request): FixedAssetResource
    {
        $asset = FixedAsset::query()->create($request->validated());

        return new FixedAssetResource($asset);
    }

    public function update(UpdateFixedAssetRequest $request, FixedAsset $fixed_asset): FixedAssetResource
    {
        $fixed_asset->update($request->validated());

        return new FixedAssetResource($fixed_asset);
    }

    public function destroy(FixedAsset $fixed_asset): array
    {
        $fixed_asset->delete();

        return ['status' => 'ok'];
    }
}

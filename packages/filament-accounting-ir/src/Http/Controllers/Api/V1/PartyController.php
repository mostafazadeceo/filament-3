<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StorePartyRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdatePartyRequest;
use Vendor\FilamentAccountingIr\Http\Resources\PartyResource;
use Vendor\FilamentAccountingIr\Models\Party;

class PartyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $parties = Party::query()->latest()->paginate();

        return PartyResource::collection($parties);
    }

    public function show(Party $party): PartyResource
    {
        return new PartyResource($party);
    }

    public function store(StorePartyRequest $request): PartyResource
    {
        $party = Party::query()->create($request->validated());

        return new PartyResource($party);
    }

    public function update(UpdatePartyRequest $request, Party $party): PartyResource
    {
        $party->update($request->validated());

        return new PartyResource($party);
    }

    public function destroy(Party $party): array
    {
        $party->delete();

        return ['status' => 'ok'];
    }
}

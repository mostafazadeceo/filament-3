<?php

namespace Haida\FilamentPettyCashIr\Http\Controllers\Api\V1;

use Haida\FilamentPettyCashIr\Http\Requests\StoreFundRequest;
use Haida\FilamentPettyCashIr\Http\Requests\UpdateFundRequest;
use Haida\FilamentPettyCashIr\Http\Resources\FundResource;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FundController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PettyCashFund::class, 'fund');
    }

    public function index(): AnonymousResourceCollection
    {
        $funds = PettyCashFund::query()->latest()->paginate();

        return FundResource::collection($funds);
    }

    public function show(PettyCashFund $fund): FundResource
    {
        return new FundResource($fund);
    }

    public function store(StoreFundRequest $request): FundResource
    {
        $fund = PettyCashFund::query()->create($request->validated());

        return new FundResource($fund);
    }

    public function update(UpdateFundRequest $request, PettyCashFund $fund): FundResource
    {
        $fund->update($request->validated());

        return new FundResource($fund->refresh());
    }

    public function destroy(PettyCashFund $fund): array
    {
        $fund->delete();

        return ['status' => 'ok'];
    }
}

<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreChartAccountRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateChartAccountRequest;
use Vendor\FilamentAccountingIr\Http\Resources\ChartAccountResource;
use Vendor\FilamentAccountingIr\Models\ChartAccount;

class ChartAccountController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $accounts = ChartAccount::query()->latest('code')->paginate();

        return ChartAccountResource::collection($accounts);
    }

    public function show(ChartAccount $chart_account): ChartAccountResource
    {
        return new ChartAccountResource($chart_account);
    }

    public function store(StoreChartAccountRequest $request): ChartAccountResource
    {
        $account = ChartAccount::query()->create($request->validated());

        return new ChartAccountResource($account);
    }

    public function update(UpdateChartAccountRequest $request, ChartAccount $chart_account): ChartAccountResource
    {
        $chart_account->update($request->validated());

        return new ChartAccountResource($chart_account);
    }

    public function destroy(ChartAccount $chart_account): array
    {
        $chart_account->delete();

        return ['status' => 'ok'];
    }
}

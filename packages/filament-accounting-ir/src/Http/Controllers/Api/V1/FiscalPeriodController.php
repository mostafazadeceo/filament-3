<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreFiscalPeriodRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateFiscalPeriodRequest;
use Vendor\FilamentAccountingIr\Http\Resources\FiscalPeriodResource;
use Vendor\FilamentAccountingIr\Models\FiscalPeriod;

class FiscalPeriodController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $periods = FiscalPeriod::query()->latest('start_date')->paginate();

        return FiscalPeriodResource::collection($periods);
    }

    public function show(FiscalPeriod $fiscal_period): FiscalPeriodResource
    {
        return new FiscalPeriodResource($fiscal_period);
    }

    public function store(StoreFiscalPeriodRequest $request): FiscalPeriodResource
    {
        $period = FiscalPeriod::query()->create($request->validated());

        return new FiscalPeriodResource($period);
    }

    public function update(UpdateFiscalPeriodRequest $request, FiscalPeriod $fiscal_period): FiscalPeriodResource
    {
        $fiscal_period->update($request->validated());

        return new FiscalPeriodResource($fiscal_period);
    }

    public function destroy(FiscalPeriod $fiscal_period): array
    {
        $fiscal_period->delete();

        return ['status' => 'ok'];
    }
}

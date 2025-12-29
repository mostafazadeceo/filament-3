<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreVatPeriodRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateVatPeriodRequest;
use Vendor\FilamentAccountingIr\Http\Resources\VatPeriodResource;
use Vendor\FilamentAccountingIr\Models\VatPeriod;
use Vendor\FilamentAccountingIr\Services\VatReportService;

class VatPeriodController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = VatPeriod::query()->with('reports')->latest('period_start')->paginate();

        return VatPeriodResource::collection($items);
    }

    public function show(VatPeriod $vat_period): VatPeriodResource
    {
        $vat_period->load('reports');

        return new VatPeriodResource($vat_period);
    }

    public function store(StoreVatPeriodRequest $request): VatPeriodResource
    {
        $item = VatPeriod::query()->create($request->validated());

        return new VatPeriodResource($item->load('reports'));
    }

    public function update(UpdateVatPeriodRequest $request, VatPeriod $vat_period): VatPeriodResource
    {
        $vat_period->update($request->validated());

        return new VatPeriodResource($vat_period->load('reports'));
    }

    public function destroy(VatPeriod $vat_period): array
    {
        $vat_period->delete();

        return ['status' => 'ok'];
    }

    public function generate(VatPeriod $vat_period): VatPeriodResource
    {
        app(VatReportService::class)->generate($vat_period);

        return new VatPeriodResource($vat_period->load('reports'));
    }
}

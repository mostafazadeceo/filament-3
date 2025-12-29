<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreVatReportRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateVatReportRequest;
use Vendor\FilamentAccountingIr\Http\Resources\VatReportResource;
use Vendor\FilamentAccountingIr\Models\VatReport;
use Vendor\FilamentAccountingIr\Services\VatReportService;

class VatReportController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = VatReport::query()->latest()->paginate();

        return VatReportResource::collection($items);
    }

    public function show(VatReport $vat_report): VatReportResource
    {
        return new VatReportResource($vat_report);
    }

    public function store(StoreVatReportRequest $request): VatReportResource
    {
        $item = VatReport::query()->create($request->validated());

        return new VatReportResource($item);
    }

    public function update(UpdateVatReportRequest $request, VatReport $vat_report): VatReportResource
    {
        $vat_report->update($request->validated());

        return new VatReportResource($vat_report);
    }

    public function destroy(VatReport $vat_report): array
    {
        $vat_report->delete();

        return ['status' => 'ok'];
    }

    public function submit(VatReport $vat_report): VatReportResource
    {
        $report = app(VatReportService::class)->submit($vat_report);

        return new VatReportResource($report);
    }
}

<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreSeasonalReportRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateSeasonalReportRequest;
use Vendor\FilamentAccountingIr\Http\Resources\SeasonalReportResource;
use Vendor\FilamentAccountingIr\Models\SeasonalReport;

class SeasonalReportController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = SeasonalReport::query()->latest('period_start')->paginate();

        return SeasonalReportResource::collection($items);
    }

    public function show(SeasonalReport $seasonal_report): SeasonalReportResource
    {
        return new SeasonalReportResource($seasonal_report);
    }

    public function store(StoreSeasonalReportRequest $request): SeasonalReportResource
    {
        $item = SeasonalReport::query()->create($request->validated());

        return new SeasonalReportResource($item);
    }

    public function update(UpdateSeasonalReportRequest $request, SeasonalReport $seasonal_report): SeasonalReportResource
    {
        $seasonal_report->update($request->validated());

        return new SeasonalReportResource($seasonal_report);
    }

    public function destroy(SeasonalReport $seasonal_report): array
    {
        $seasonal_report->delete();

        return ['status' => 'ok'];
    }
}

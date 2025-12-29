<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreFiscalYearRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateFiscalYearRequest;
use Vendor\FilamentAccountingIr\Http\Resources\FiscalYearResource;
use Vendor\FilamentAccountingIr\Models\FiscalYear;

class FiscalYearController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $years = FiscalYear::query()->latest('start_date')->paginate();

        return FiscalYearResource::collection($years);
    }

    public function show(FiscalYear $fiscal_year): FiscalYearResource
    {
        return new FiscalYearResource($fiscal_year);
    }

    public function store(StoreFiscalYearRequest $request): FiscalYearResource
    {
        $year = FiscalYear::query()->create($request->validated());

        return new FiscalYearResource($year);
    }

    public function update(UpdateFiscalYearRequest $request, FiscalYear $fiscal_year): FiscalYearResource
    {
        $fiscal_year->update($request->validated());

        return new FiscalYearResource($fiscal_year);
    }

    public function destroy(FiscalYear $fiscal_year): array
    {
        $fiscal_year->delete();

        return ['status' => 'ok'];
    }
}

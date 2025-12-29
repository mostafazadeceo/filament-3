<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StorePayrollTableRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdatePayrollTableRequest;
use Vendor\FilamentAccountingIr\Http\Resources\PayrollTableResource;
use Vendor\FilamentAccountingIr\Models\PayrollTable;

class PayrollTableController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = PayrollTable::query()->latest('effective_from')->paginate();

        return PayrollTableResource::collection($items);
    }

    public function show(PayrollTable $payroll_table): PayrollTableResource
    {
        return new PayrollTableResource($payroll_table);
    }

    public function store(StorePayrollTableRequest $request): PayrollTableResource
    {
        $item = PayrollTable::query()->create($request->validated());

        return new PayrollTableResource($item);
    }

    public function update(UpdatePayrollTableRequest $request, PayrollTable $payroll_table): PayrollTableResource
    {
        $payroll_table->update($request->validated());

        return new PayrollTableResource($payroll_table);
    }

    public function destroy(PayrollTable $payroll_table): array
    {
        $payroll_table->delete();

        return ['status' => 'ok'];
    }
}

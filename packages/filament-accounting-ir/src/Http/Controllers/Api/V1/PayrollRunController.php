<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StorePayrollRunRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdatePayrollRunRequest;
use Vendor\FilamentAccountingIr\Http\Resources\PayrollRunResource;
use Vendor\FilamentAccountingIr\Models\PayrollRun;

class PayrollRunController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = PayrollRun::query()->latest('run_date')->paginate();

        return PayrollRunResource::collection($items);
    }

    public function show(PayrollRun $payroll_run): PayrollRunResource
    {
        return new PayrollRunResource($payroll_run);
    }

    public function store(StorePayrollRunRequest $request): PayrollRunResource
    {
        $item = PayrollRun::query()->create($request->validated());

        return new PayrollRunResource($item);
    }

    public function update(UpdatePayrollRunRequest $request, PayrollRun $payroll_run): PayrollRunResource
    {
        $payroll_run->update($request->validated());

        return new PayrollRunResource($payroll_run);
    }

    public function destroy(PayrollRun $payroll_run): array
    {
        $payroll_run->delete();

        return ['status' => 'ok'];
    }
}

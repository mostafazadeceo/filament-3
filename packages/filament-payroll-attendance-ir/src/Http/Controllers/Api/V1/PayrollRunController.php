<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StorePayrollRunRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdatePayrollRunRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\PayrollRunResource;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollRun;
use Vendor\FilamentPayrollAttendanceIr\Services\PayrollRunService;
use Vendor\FilamentPayrollAttendanceIr\Services\PayrollWebhookService;

class PayrollRunController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PayrollRun::class, 'payroll_run');
    }

    public function index(): AnonymousResourceCollection
    {
        $runs = PayrollRun::query()->latest()->paginate();

        return PayrollRunResource::collection($runs);
    }

    public function show(PayrollRun $payroll_run): PayrollRunResource
    {
        return new PayrollRunResource($payroll_run);
    }

    public function store(StorePayrollRunRequest $request): PayrollRunResource
    {
        $run = PayrollRun::query()->create($request->validated());

        return new PayrollRunResource($run);
    }

    public function update(UpdatePayrollRunRequest $request, PayrollRun $payroll_run): PayrollRunResource
    {
        $payroll_run->update($request->validated());

        return new PayrollRunResource($payroll_run->refresh());
    }

    public function destroy(PayrollRun $payroll_run): array
    {
        $payroll_run->delete();

        return ['status' => 'ok'];
    }

    public function generate(PayrollRun $payroll_run): PayrollRunResource
    {
        $this->authorize('update', $payroll_run);

        app(PayrollRunService::class)->generate($payroll_run);

        return new PayrollRunResource($payroll_run->refresh());
    }

    public function approve(PayrollRun $payroll_run): PayrollRunResource
    {
        $this->authorize('approve', $payroll_run);

        $payroll_run->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return new PayrollRunResource($payroll_run->refresh());
    }

    public function post(PayrollRun $payroll_run): PayrollRunResource
    {
        $this->authorize('post', $payroll_run);

        $payroll_run->update([
            'status' => 'posted',
            'posted_at' => now(),
        ]);

        app(PayrollWebhookService::class)->dispatch('payroll.run.posted', $payroll_run->company_id, [
            'id' => $payroll_run->getKey(),
            'period_start' => $payroll_run->period_start,
            'period_end' => $payroll_run->period_end,
            'status' => $payroll_run->status,
        ]);

        return new PayrollRunResource($payroll_run->refresh());
    }

    public function lock(PayrollRun $payroll_run): PayrollRunResource
    {
        $this->authorize('lock', $payroll_run);

        $payroll_run->update([
            'status' => 'locked',
            'locked_at' => now(),
        ]);

        return new PayrollRunResource($payroll_run->refresh());
    }
}

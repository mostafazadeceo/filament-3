<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreLoanRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateLoanRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\LoanResource;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLoan;

class LoanController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PayrollLoan::class, 'loan');
    }

    public function index(): AnonymousResourceCollection
    {
        $loans = PayrollLoan::query()->latest()->paginate();

        return LoanResource::collection($loans);
    }

    public function show(PayrollLoan $loan): LoanResource
    {
        return new LoanResource($loan);
    }

    public function store(StoreLoanRequest $request): LoanResource
    {
        $loan = PayrollLoan::query()->create($request->validated());

        return new LoanResource($loan);
    }

    public function update(UpdateLoanRequest $request, PayrollLoan $loan): LoanResource
    {
        $loan->update($request->validated());

        return new LoanResource($loan->refresh());
    }

    public function destroy(PayrollLoan $loan): array
    {
        $loan->delete();

        return ['status' => 'ok'];
    }
}

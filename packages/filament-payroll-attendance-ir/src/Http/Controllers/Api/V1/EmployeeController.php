<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreEmployeeRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateEmployeeRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\EmployeeResource;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class EmployeeController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PayrollEmployee::class, 'employee');
    }

    public function index(): AnonymousResourceCollection
    {
        $employees = PayrollEmployee::query()->latest()->paginate();

        return EmployeeResource::collection($employees);
    }

    public function show(PayrollEmployee $employee): EmployeeResource
    {
        $this->logSensitiveAccess($employee);

        return new EmployeeResource($employee);
    }

    public function store(StoreEmployeeRequest $request): EmployeeResource
    {
        $employee = PayrollEmployee::query()->create($request->validated());

        return new EmployeeResource($employee);
    }

    public function update(UpdateEmployeeRequest $request, PayrollEmployee $employee): EmployeeResource
    {
        $employee->update($request->validated());

        return new EmployeeResource($employee->refresh());
    }

    public function destroy(PayrollEmployee $employee): array
    {
        $employee->delete();

        return ['status' => 'ok'];
    }
}

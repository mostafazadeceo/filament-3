<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreEmployeeRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateEmployeeRequest;
use Vendor\FilamentAccountingIr\Http\Resources\EmployeeResource;
use Vendor\FilamentAccountingIr\Models\Employee;

class EmployeeController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $employees = Employee::query()->latest()->paginate();

        return EmployeeResource::collection($employees);
    }

    public function show(Employee $employee): EmployeeResource
    {
        return new EmployeeResource($employee);
    }

    public function store(StoreEmployeeRequest $request): EmployeeResource
    {
        $employee = Employee::query()->create($request->validated());

        return new EmployeeResource($employee);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): EmployeeResource
    {
        $employee->update($request->validated());

        return new EmployeeResource($employee);
    }

    public function destroy(Employee $employee): array
    {
        $employee->delete();

        return ['status' => 'ok'];
    }
}

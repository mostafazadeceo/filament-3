<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreScheduleRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateScheduleRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\ScheduleResource;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceSchedule;

class ScheduleController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PayrollAttendanceSchedule::class, 'schedule');
    }

    public function index(): AnonymousResourceCollection
    {
        $schedules = PayrollAttendanceSchedule::query()->latest()->paginate();

        return ScheduleResource::collection($schedules);
    }

    public function show(PayrollAttendanceSchedule $schedule): ScheduleResource
    {
        return new ScheduleResource($schedule);
    }

    public function store(StoreScheduleRequest $request): ScheduleResource
    {
        $schedule = PayrollAttendanceSchedule::query()->create($request->validated());

        return new ScheduleResource($schedule);
    }

    public function update(UpdateScheduleRequest $request, PayrollAttendanceSchedule $schedule): ScheduleResource
    {
        $schedule->update($request->validated());

        return new ScheduleResource($schedule->refresh());
    }

    public function destroy(PayrollAttendanceSchedule $schedule): array
    {
        $schedule->delete();

        return ['status' => 'ok'];
    }
}

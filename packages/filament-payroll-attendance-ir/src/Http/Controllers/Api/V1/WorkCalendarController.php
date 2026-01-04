<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\WorkCalendar;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreWorkCalendarRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateWorkCalendarRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\WorkCalendarResource;

class WorkCalendarController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(WorkCalendar::class, 'work_calendar');
    }

    public function index(): AnonymousResourceCollection
    {
        $calendars = WorkCalendar::query()->latest()->paginate();

        return WorkCalendarResource::collection($calendars);
    }

    public function show(WorkCalendar $work_calendar): WorkCalendarResource
    {
        return new WorkCalendarResource($work_calendar);
    }

    public function store(StoreWorkCalendarRequest $request): WorkCalendarResource
    {
        $calendar = WorkCalendar::query()->create($request->validated());

        return new WorkCalendarResource($calendar);
    }

    public function update(UpdateWorkCalendarRequest $request, WorkCalendar $work_calendar): WorkCalendarResource
    {
        $work_calendar->update($request->validated());

        return new WorkCalendarResource($work_calendar->refresh());
    }

    public function destroy(WorkCalendar $work_calendar): array
    {
        $work_calendar->delete();

        return ['status' => 'ok'];
    }
}

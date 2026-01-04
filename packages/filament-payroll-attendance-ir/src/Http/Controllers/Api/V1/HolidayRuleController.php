<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\HolidayRule;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\WorkCalendar;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreHolidayRuleRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateHolidayRuleRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\HolidayRuleResource;

class HolidayRuleController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(HolidayRule::class, 'holiday_rule');
    }

    public function index(): AnonymousResourceCollection
    {
        $rules = HolidayRule::query()->latest('holiday_date')->paginate();

        return HolidayRuleResource::collection($rules);
    }

    public function show(HolidayRule $holiday_rule): HolidayRuleResource
    {
        return new HolidayRuleResource($holiday_rule);
    }

    public function store(StoreHolidayRuleRequest $request): HolidayRuleResource
    {
        $payload = $request->validated();
        $this->assertCalendarCompany($payload['work_calendar_id'], $payload['company_id']);

        $rule = HolidayRule::query()->create($payload);

        return new HolidayRuleResource($rule);
    }

    public function update(UpdateHolidayRuleRequest $request, HolidayRule $holiday_rule): HolidayRuleResource
    {
        $payload = $request->validated();
        if (array_key_exists('work_calendar_id', $payload)) {
            $companyId = $payload['company_id'] ?? $holiday_rule->company_id;
            $this->assertCalendarCompany($payload['work_calendar_id'], (int) $companyId);
        }

        $holiday_rule->update($payload);

        return new HolidayRuleResource($holiday_rule->refresh());
    }

    public function destroy(HolidayRule $holiday_rule): array
    {
        $holiday_rule->delete();

        return ['status' => 'ok'];
    }

    private function assertCalendarCompany(int $calendarId, int $companyId): void
    {
        $exists = WorkCalendar::query()
            ->whereKey($calendarId)
            ->where('company_id', $companyId)
            ->exists();

        if (! $exists) {
            abort(422, 'Invalid work_calendar_id for company.');
        }
    }
}

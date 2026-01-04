<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HolidayRuleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'work_calendar_id' => $this->work_calendar_id,
            'holiday_date' => $this->holiday_date,
            'title' => $this->title,
            'is_public' => (bool) $this->is_public,
            'source' => $this->source,
            'metadata' => $this->metadata,
        ];
    }
}

<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShiftResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'name' => $this->name,
            'code' => $this->code,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'break_minutes' => $this->break_minutes,
            'is_night' => $this->is_night,
            'is_rotating' => $this->is_rotating,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}

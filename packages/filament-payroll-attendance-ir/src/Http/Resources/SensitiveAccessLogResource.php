<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SensitiveAccessLogResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'actor_id' => $this->actor_id,
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id,
            'reason' => $this->reason,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
        ];
    }
}

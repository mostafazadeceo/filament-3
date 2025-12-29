<?php

namespace Haida\FilamentWorkhub\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'work_item_id' => $this->work_item_id,
            'user_id' => $this->user_id,
            'minutes' => $this->minutes,
            'started_at' => optional($this->started_at)->toIso8601String(),
            'ended_at' => optional($this->ended_at)->toIso8601String(),
            'note' => $this->note,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}

<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DimensionValueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dimension_id' => $this->dimension_id,
            'code' => $this->code,
            'name' => $this->name,
            'is_active' => (bool) $this->is_active,
        ];
    }
}

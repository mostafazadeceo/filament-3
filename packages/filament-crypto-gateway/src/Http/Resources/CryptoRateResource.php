<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CryptoRateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'from_currency' => $this->from,
            'to_currency' => $this->to,
            'rate' => $this->rate,
            'source' => $this->source,
            'quoted_at' => $this->quoted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

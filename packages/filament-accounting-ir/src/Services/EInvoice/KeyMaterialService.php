<?php

namespace Vendor\FilamentAccountingIr\Services\EInvoice;

use Illuminate\Support\Carbon;
use Vendor\FilamentAccountingIr\Models\KeyMaterial;

class KeyMaterialService
{
    public function getActive(int $companyId, string $materialType, ?Carbon $date = null): ?KeyMaterial
    {
        $date ??= now();

        return KeyMaterial::query()
            ->where('company_id', $companyId)
            ->where('material_type', $materialType)
            ->where(function ($query) use ($date) {
                $query->whereNull('effective_from')->orWhere('effective_from', '<=', $date->toDateString());
            })
            ->where(function ($query) use ($date) {
                $query->whereNull('effective_to')->orWhere('effective_to', '>=', $date->toDateString());
            })
            ->orderByDesc('effective_from')
            ->first();
    }
}

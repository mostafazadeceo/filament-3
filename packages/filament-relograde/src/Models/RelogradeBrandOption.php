<?php

namespace Haida\FilamentRelograde\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelogradeBrandOption extends Model
{
    protected $table = 'relograde_brand_options';

    protected $fillable = [
        'brand_id',
        'redeem_value',
        'raw_json',
    ];

    protected function casts(): array
    {
        return [
            'redeem_value' => 'string',
            'raw_json' => 'array',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(RelogradeBrand::class, 'brand_id');
    }
}

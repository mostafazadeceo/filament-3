<?php

namespace Haida\FilamentRelograde\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelogradeProduct extends Model
{
    protected $table = 'relograde_products';

    protected $fillable = [
        'connection_id',
        'slug',
        'name',
        'brand_slug',
        'brand_name',
        'category',
        'redeem_type',
        'redeem_value',
        'is_stocked',
        'is_variable_product',
        'face_value_currency',
        'face_value_amount',
        'face_value_min',
        'face_value_max',
        'price_amount',
        'price_currency',
        'fee_variable',
        'fee_fixed',
        'fee_currency',
        'raw_json',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'redeem_value' => 'string',
            'is_stocked' => 'boolean',
            'is_variable_product' => 'boolean',
            'face_value_amount' => 'decimal:4',
            'face_value_min' => 'decimal:4',
            'face_value_max' => 'decimal:4',
            'price_amount' => 'decimal:4',
            'fee_variable' => 'decimal:4',
            'fee_fixed' => 'decimal:4',
            'raw_json' => 'array',
            'synced_at' => 'datetime',
        ];
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(RelogradeConnection::class, 'connection_id');
    }
}

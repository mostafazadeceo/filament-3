<?php

namespace Haida\FilamentRelograde\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RelogradeOrderItem extends Model
{
    protected $table = 'relograde_order_items';

    protected $fillable = [
        'order_id',
        'product_slug',
        'product_name',
        'brand',
        'product_type',
        'region',
        'redeem_type',
        'main_category',
        'amount',
        'face_value_amount',
        'face_value_currency',
        'face_value_fx',
        'single_price_amount',
        'total_price_amount',
        'total_price_vat',
        'total_price_incl_vat',
        'price_fx',
        'payment_currency',
        'single_price_amount_in_payment_currency',
        'total_price_amount_in_payment_currency',
        'total_price_vat_in_payment_currency',
        'total_price_incl_vat_in_payment_currency',
        'lines_completed',
        'raw_json',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'face_value_amount' => 'decimal:4',
            'face_value_fx' => 'decimal:6',
            'single_price_amount' => 'decimal:4',
            'total_price_amount' => 'decimal:4',
            'total_price_vat' => 'decimal:4',
            'total_price_incl_vat' => 'decimal:4',
            'price_fx' => 'decimal:6',
            'single_price_amount_in_payment_currency' => 'decimal:4',
            'total_price_amount_in_payment_currency' => 'decimal:4',
            'total_price_vat_in_payment_currency' => 'decimal:4',
            'total_price_incl_vat_in_payment_currency' => 'decimal:4',
            'lines_completed' => 'integer',
            'raw_json' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(RelogradeOrder::class, 'order_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(RelogradeOrderLine::class, 'order_item_id');
    }
}

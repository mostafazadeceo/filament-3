<?php

namespace Haida\FilamentRelograde\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelogradeOrderLine extends Model
{
    protected $table = 'relograde_order_lines';

    protected $fillable = [
        'order_item_id',
        'tag',
        'status',
        'voucher_code',
        'voucher_serial',
        'voucher_date_expired',
        'token',
        'voucher_url',
        'raw_json',
    ];

    protected function casts(): array
    {
        $casts = [
            'voucher_date_expired' => 'datetime',
            'raw_json' => 'array',
        ];

        if (config('relograde.encrypt_voucher_codes', true)) {
            $casts['voucher_code'] = 'encrypted';
        }

        return $casts;
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(RelogradeOrderItem::class, 'order_item_id');
    }
}

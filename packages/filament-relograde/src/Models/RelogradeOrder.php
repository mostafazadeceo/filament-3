<?php

namespace Haida\FilamentRelograde\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RelogradeOrder extends Model
{
    protected $table = 'relograde_orders';

    protected $fillable = [
        'connection_id',
        'trx',
        'reference',
        'state',
        'type',
        'order_status',
        'payment_status',
        'is_balance_payment',
        'downloaded',
        'payment_currency',
        'price_currency',
        'price_amount',
        'price_vat',
        'price_incl_vat',
        'price_fx',
        'date_created',
        'last_synced_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'is_balance_payment' => 'boolean',
            'downloaded' => 'boolean',
            'price_amount' => 'decimal:4',
            'price_vat' => 'decimal:4',
            'price_incl_vat' => 'decimal:4',
            'price_fx' => 'decimal:6',
            'date_created' => 'datetime',
            'last_synced_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(RelogradeConnection::class, 'connection_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RelogradeOrderItem::class, 'order_id');
    }
}

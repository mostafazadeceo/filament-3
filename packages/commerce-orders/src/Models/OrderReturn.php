<?php

namespace Haida\CommerceOrders\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderReturn extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'order_id',
        'status',
        'reason',
        'notes',
        'requested_at',
        'approved_at',
        'rejected_at',
        'received_at',
        'refunded_at',
        'requested_by_user_id',
        'approved_by_user_id',
        'rejected_by_user_id',
        'received_by_user_id',
        'refunded_by_user_id',
        'meta',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'received_at' => 'datetime',
        'refunded_at' => 'datetime',
        'meta' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderReturnItem::class, 'order_return_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(OrderRefund::class, 'order_return_id');
    }

    public function getTable(): string
    {
        return config('commerce-orders.tables.order_returns', 'commerce_order_returns');
    }
}

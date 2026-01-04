<?php

namespace Haida\CommerceOrders\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRefund extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'order_id',
        'order_return_id',
        'order_payment_id',
        'status',
        'currency',
        'amount',
        'provider',
        'reference',
        'reason',
        'idempotency_key',
        'notes',
        'processed_at',
        'created_by_user_id',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'processed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function orderReturn(): BelongsTo
    {
        return $this->belongsTo(OrderReturn::class, 'order_return_id');
    }

    public function orderPayment(): BelongsTo
    {
        return $this->belongsTo(OrderPayment::class, 'order_payment_id');
    }

    public function getTable(): string
    {
        return config('commerce-orders.tables.order_refunds', 'commerce_order_refunds');
    }
}

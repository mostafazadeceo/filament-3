<?php

namespace Haida\CommerceOrders\Models;

use Filamat\IamSuite\Models\WalletHold;
use Filamat\IamSuite\Models\WalletTransaction;
use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderPayment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'order_id',
        'method',
        'status',
        'currency',
        'amount',
        'provider',
        'reference',
        'wallet_transaction_id',
        'wallet_hold_id',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'meta' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class, 'wallet_transaction_id');
    }

    public function walletHold(): BelongsTo
    {
        return $this->belongsTo(WalletHold::class, 'wallet_hold_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(OrderRefund::class, 'order_payment_id');
    }

    public function getTable(): string
    {
        return config('commerce-orders.tables.order_payments', 'commerce_order_payments');
    }
}

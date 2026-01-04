<?php

namespace Haida\FilamentPayments\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRefund extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'payment_intent_id',
        'status',
        'currency',
        'amount',
        'provider',
        'reference',
        'idempotency_key',
        'processed_at',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'processed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function intent(): BelongsTo
    {
        return $this->belongsTo(PaymentIntent::class, 'payment_intent_id');
    }

    public function getTable(): string
    {
        return config('filament-payments.tables.refunds', 'payments_refunds');
    }
}

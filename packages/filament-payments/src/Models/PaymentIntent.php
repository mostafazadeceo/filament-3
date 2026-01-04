<?php

namespace Haida\FilamentPayments\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentIntent extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'reference_type',
        'reference_id',
        'status',
        'provider',
        'provider_reference',
        'currency',
        'amount',
        'idempotency_key',
        'redirect_url',
        'expires_at',
        'confirmed_at',
        'cancelled_at',
        'failed_at',
        'created_by_user_id',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function attempts(): HasMany
    {
        return $this->hasMany(PaymentAttempt::class, 'payment_intent_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(PaymentRefund::class, 'payment_intent_id');
    }

    public function getTable(): string
    {
        return config('filament-payments.tables.payment_intents', 'payments_payment_intents');
    }
}

<?php

namespace Haida\FilamentPayments\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAttempt extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'payment_intent_id',
        'status',
        'provider',
        'provider_reference',
        'payload',
        'response',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
    ];

    public function intent(): BelongsTo
    {
        return $this->belongsTo(PaymentIntent::class, 'payment_intent_id');
    }

    public function getTable(): string
    {
        return config('filament-payments.tables.payment_attempts', 'payments_payment_attempts');
    }
}

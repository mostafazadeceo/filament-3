<?php

namespace Haida\FilamentPayments\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PaymentWebhookEvent extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'provider',
        'event_type',
        'external_id',
        'signature_valid',
        'status',
        'headers',
        'payload',
        'received_at',
        'processed_at',
    ];

    protected $casts = [
        'signature_valid' => 'bool',
        'headers' => 'array',
        'payload' => 'array',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('filament-payments.tables.webhook_events', 'payments_webhook_events');
    }
}

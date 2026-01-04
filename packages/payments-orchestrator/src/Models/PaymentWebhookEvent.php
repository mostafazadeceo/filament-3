<?php

namespace Haida\PaymentsOrchestrator\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PaymentWebhookEvent extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'provider_key',
        'event_id',
        'signature',
        'payload',
        'headers',
        'status',
        'idempotency_key',
        'received_at',
        'processed_at',
        'error_message',
    ];

    protected $casts = [
        'headers' => 'array',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('payments-orchestrator.tables.webhook_events', 'payment_webhook_events');
    }
}

<?php

namespace Haida\FilamentCryptoGateway\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Haida\FilamentCryptoGateway\Enums\CryptoWebhookCallStatus;
use Illuminate\Database\Eloquent\Model;

class CryptoWebhookCall extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'provider',
        'event_id',
        'signature_ok',
        'ip_ok',
        'idempotency_key',
        'payload_hash',
        'headers_json',
        'remote_ip',
        'raw_payload',
        'payload_json',
        'received_at',
        'processed_at',
        'status',
        'error',
        'retry_count',
    ];

    protected $casts = [
        'signature_ok' => 'bool',
        'ip_ok' => 'bool',
        'headers_json' => 'array',
        'payload_json' => 'array',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
        'status' => CryptoWebhookCallStatus::class,
    ];

    public function getTable(): string
    {
        return config('filament-crypto-gateway.tables.webhook_calls', 'crypto_webhook_calls');
    }
}

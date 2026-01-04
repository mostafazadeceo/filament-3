<?php

namespace Haida\FilamentCryptoGateway\Models;

use Haida\FilamentCryptoCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoInvoicePayment extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'txid',
        'from_address',
        'payer_amount',
        'payer_currency',
        'confirmations',
        'status',
        'raw_payload_json',
        'seen_at',
    ];

    protected $casts = [
        'payer_amount' => 'decimal:8',
        'confirmations' => 'int',
        'raw_payload_json' => 'array',
        'seen_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(CryptoInvoice::class, 'invoice_id');
    }

    public function getTable(): string
    {
        return config('filament-crypto-gateway.tables.invoice_payments', 'crypto_invoice_payments');
    }
}

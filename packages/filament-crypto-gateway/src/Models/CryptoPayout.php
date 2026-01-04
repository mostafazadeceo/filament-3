<?php

namespace Haida\FilamentCryptoGateway\Models;

use Haida\FilamentCryptoCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;

class CryptoPayout extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'provider',
        'order_id',
        'external_uuid',
        'to_address',
        'amount',
        'currency',
        'network',
        'fee',
        'status',
        'is_final',
        'approved_at',
        'approved_by',
        'approval_note',
        'fail_reason',
        'txid',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'fee' => 'decimal:8',
        'is_final' => 'bool',
        'approved_at' => 'datetime',
        'approved_by' => 'integer',
        'meta' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-crypto-gateway.tables.payouts', 'crypto_payouts');
    }
}

<?php

namespace Haida\FilamentCryptoGateway\Models;

use Haida\FilamentCryptoCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;

class CryptoReconciliation extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'scope',
        'started_at',
        'ended_at',
        'result_json',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'result_json' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-crypto-gateway.tables.reconciliations', 'crypto_reconciliations');
    }
}

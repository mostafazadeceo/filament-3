<?php

namespace Haida\FilamentCryptoCore\Models;

use Haida\FilamentCryptoCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;

class CryptoFeePolicy extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'plan_key',
        'invoice_percent',
        'invoice_fixed',
        'payout_fixed',
        'conversion_percent',
        'network_fee_mode',
        'meta',
    ];

    protected $casts = [
        'invoice_percent' => 'decimal:4',
        'invoice_fixed' => 'decimal:8',
        'payout_fixed' => 'decimal:8',
        'conversion_percent' => 'decimal:4',
        'meta' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-crypto-core.tables.fee_policies', 'crypto_fee_policies');
    }
}

<?php

namespace Haida\FilamentCryptoCore\Models;

use Haida\FilamentCryptoCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CryptoWallet extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'mode',
        'provider',
        'label',
        'currency',
        'network',
        'status',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(CryptoAddress::class, 'wallet_id');
    }

    public function getTable(): string
    {
        return config('filament-crypto-core.tables.wallets', 'crypto_wallets');
    }
}

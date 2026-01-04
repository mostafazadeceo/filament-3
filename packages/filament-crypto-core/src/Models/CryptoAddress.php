<?php

namespace Haida\FilamentCryptoCore\Models;

use Haida\FilamentCryptoCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoAddress extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'wallet_id',
        'address',
        'tag_memo',
        'derivation_path',
        'status',
        'last_seen_at',
        'meta',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'meta' => 'array',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(CryptoWallet::class, 'wallet_id');
    }

    public function getTable(): string
    {
        return config('filament-crypto-core.tables.addresses', 'crypto_addresses');
    }
}

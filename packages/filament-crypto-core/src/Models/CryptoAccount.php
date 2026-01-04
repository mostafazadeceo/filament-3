<?php

namespace Haida\FilamentCryptoCore\Models;

use Haida\FilamentCryptoCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;

class CryptoAccount extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'code',
        'name_fa',
        'type',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-crypto-core.tables.accounts', 'crypto_accounts');
    }
}

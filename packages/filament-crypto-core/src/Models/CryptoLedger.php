<?php

namespace Haida\FilamentCryptoCore\Models;

use Haida\FilamentCryptoCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CryptoLedger extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'ref_type',
        'ref_id',
        'occurred_at',
        'description',
        'meta',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'meta' => 'array',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(CryptoLedgerEntry::class, 'ledger_id');
    }

    public function getTable(): string
    {
        return config('filament-crypto-core.tables.ledgers', 'crypto_ledgers');
    }
}

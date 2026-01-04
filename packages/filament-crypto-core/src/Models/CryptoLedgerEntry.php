<?php

namespace Haida\FilamentCryptoCore\Models;

use Haida\FilamentCryptoCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoLedgerEntry extends Model
{
    use UsesTenant;

    protected $fillable = [
        'ledger_id',
        'tenant_id',
        'account_id',
        'debit',
        'credit',
        'currency',
        'meta',
    ];

    protected $casts = [
        'debit' => 'decimal:8',
        'credit' => 'decimal:8',
        'meta' => 'array',
    ];

    public function ledger(): BelongsTo
    {
        return $this->belongsTo(CryptoLedger::class, 'ledger_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(CryptoAccount::class, 'account_id');
    }

    public function getTable(): string
    {
        return config('filament-crypto-core.tables.ledger_entries', 'crypto_ledger_entries');
    }
}

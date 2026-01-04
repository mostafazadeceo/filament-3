<?php

namespace Haida\FilamentCryptoGateway\Models;

use Haida\FilamentCryptoCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CryptoInvoice extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'provider',
        'order_id',
        'external_uuid',
        'amount',
        'currency',
        'to_currency',
        'network',
        'address',
        'status',
        'is_final',
        'expires_at',
        'tolerance_percent',
        'subtract_percent',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'is_final' => 'bool',
        'expires_at' => 'datetime',
        'tolerance_percent' => 'decimal:4',
        'subtract_percent' => 'decimal:4',
        'meta' => 'array',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(CryptoInvoicePayment::class, 'invoice_id');
    }

    public function getTable(): string
    {
        return config('filament-crypto-gateway.tables.invoices', 'crypto_invoices');
    }
}

<?php

namespace Haida\FilamentPayments\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PaymentReconciliation extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'provider',
        'period_start',
        'period_end',
        'status',
        'summary',
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'summary' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-payments.tables.reconciliations', 'payments_reconciliations');
    }
}

<?php

namespace Haida\FilamentPayments\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PaymentProviderConnection extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'provider_key',
        'display_name',
        'credentials',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'meta' => 'array',
        'is_active' => 'bool',
    ];

    public function getTable(): string
    {
        return config('filament-payments.tables.provider_connections', 'payments_provider_connections');
    }
}

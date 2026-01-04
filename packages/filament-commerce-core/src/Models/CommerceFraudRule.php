<?php

namespace Haida\FilamentCommerceCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class CommerceFraudRule extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'key',
        'name',
        'status',
        'thresholds',
        'metadata',
    ];

    protected $casts = [
        'thresholds' => 'array',
        'metadata' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-commerce-core.tables.fraud_rules', 'commerce_fraud_rules');
    }
}

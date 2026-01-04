<?php

namespace Haida\FilamentCommerceCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommerceCustomer extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'email',
        'phone',
        'status',
        'default_billing_address',
        'default_shipping_address',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'default_billing_address' => 'array',
        'default_shipping_address' => 'array',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function getTable(): string
    {
        return config('filament-commerce-core.tables.customers', 'commerce_customers');
    }
}

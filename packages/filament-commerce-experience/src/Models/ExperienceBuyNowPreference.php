<?php

namespace Haida\FilamentCommerceExperience\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ExperienceBuyNowPreference extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'default_address_id',
        'default_payment_provider',
        'status',
        'requires_2fa',
        'consent_at',
        'consent_ip',
        'metadata',
    ];

    protected $casts = [
        'requires_2fa' => 'bool',
        'consent_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-commerce-experience.tables.buy_now_preferences', 'exp_buy_now_preferences');
    }
}

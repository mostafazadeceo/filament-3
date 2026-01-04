<?php

namespace Haida\FilamentCommerceCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommerceBrand extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(CommerceProduct::class, 'brand_id');
    }

    public function getTable(): string
    {
        return config('filament-commerce-core.tables.brands', 'commerce_brands');
    }
}

<?php

namespace Haida\FilamentStorefrontBuilder\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreMenu extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'key',
        'name',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(StoreMenuItem::class, 'menu_id');
    }

    public function getTable(): string
    {
        return config('filament-storefront-builder.tables.menus', 'store_menus');
    }
}

<?php

namespace Haida\FilamentStorefrontBuilder\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class StoreTheme extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'status',
        'config',
        'metadata',
        'activated_at',
    ];

    protected $casts = [
        'config' => 'array',
        'metadata' => 'array',
        'activated_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('filament-storefront-builder.tables.themes', 'store_themes');
    }
}

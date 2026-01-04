<?php

namespace Haida\FilamentStorefrontBuilder\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreMenuItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'menu_id',
        'parent_id',
        'page_id',
        'label',
        'url',
        'sort_order',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(StoreMenu::class, 'menu_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(StoreMenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(StoreMenuItem::class, 'parent_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(StorePage::class, 'page_id');
    }

    public function getTable(): string
    {
        return config('filament-storefront-builder.tables.menu_items', 'store_menu_items');
    }
}

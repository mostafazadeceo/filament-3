<?php

namespace Haida\FilamentCommerceCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommerceStockMove extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'inventory_item_id',
        'type',
        'quantity',
        'reason',
        'reference_type',
        'reference_id',
        'actor_user_id',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'metadata' => 'array',
    ];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(CommerceInventoryItem::class, 'inventory_item_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'actor_user_id');
    }

    public function getTable(): string
    {
        return config('filament-commerce-core.tables.stock_moves', 'commerce_stock_moves');
    }
}

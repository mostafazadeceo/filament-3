<?php

namespace Haida\CommerceCatalog\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogMedia extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'type',
        'url',
        'alt',
        'sort_order',
        'is_primary',
        'metadata',
    ];

    protected $casts = [
        'sort_order' => 'int',
        'is_primary' => 'bool',
        'metadata' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(CatalogProduct::class, 'product_id');
    }

    public function getTable(): string
    {
        return config('commerce-catalog.tables.media', 'commerce_catalog_media');
    }
}

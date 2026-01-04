<?php

namespace Haida\CommerceCatalog\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CatalogCollection extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'site_id',
        'name',
        'slug',
        'status',
        'description',
        'published_at',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            CatalogProduct::class,
            config('commerce-catalog.tables.collection_product', 'commerce_catalog_collection_product'),
            'collection_id',
            'product_id'
        )->withTimestamps();
    }

    public function getTable(): string
    {
        return config('commerce-catalog.tables.collections', 'commerce_catalog_collections');
    }
}

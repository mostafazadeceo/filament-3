<?php

namespace Haida\FilamentStorefrontBuilder\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorePage extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'site_id',
        'title',
        'slug',
        'status',
        'blocks',
        'seo',
        'published_at',
        'scheduled_publish_at',
        'version',
        'created_by_user_id',
        'updated_by_user_id',
        'metadata',
    ];

    protected $casts = [
        'blocks' => 'array',
        'seo' => 'array',
        'metadata' => 'array',
        'published_at' => 'datetime',
        'scheduled_publish_at' => 'datetime',
    ];

    public function versions(): HasMany
    {
        return $this->hasMany(StorePageVersion::class, 'page_id');
    }

    public function getTable(): string
    {
        return config('filament-storefront-builder.tables.pages', 'store_pages');
    }
}

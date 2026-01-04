<?php

namespace Haida\FilamentStorefrontBuilder\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorePageVersion extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'page_id',
        'version',
        'blocks',
        'seo',
        'status',
        'created_by_user_id',
    ];

    protected $casts = [
        'blocks' => 'array',
        'seo' => 'array',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(StorePage::class, 'page_id');
    }

    public function getTable(): string
    {
        return config('filament-storefront-builder.tables.page_versions', 'store_page_versions');
    }
}

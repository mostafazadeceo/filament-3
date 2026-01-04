<?php

namespace Haida\FilamentStorefrontBuilder\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class StoreBlock extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'key',
        'type',
        'name',
        'status',
        'schema',
        'content',
        'metadata',
    ];

    protected $casts = [
        'schema' => 'array',
        'content' => 'array',
        'metadata' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-storefront-builder.tables.blocks', 'store_blocks');
    }
}

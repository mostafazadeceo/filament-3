<?php

namespace Haida\FilamentStorefrontBuilder\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class StoreRedirect extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'from_path',
        'to_path',
        'status_code',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-storefront-builder.tables.redirects', 'store_redirects');
    }
}

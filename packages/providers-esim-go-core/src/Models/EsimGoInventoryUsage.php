<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class EsimGoInventoryUsage extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'usage_id',
        'bundle_name',
        'remaining',
        'expiry_at',
        'countries',
        'fetched_at',
    ];

    protected $casts = [
        'countries' => 'array',
        'expiry_at' => 'datetime',
        'fetched_at' => 'datetime',
        'remaining' => 'decimal:4',
    ];

    public function getTable(): string
    {
        return config('providers-esim-go-core.tables.inventory_usages', 'esim_go_inventory_usages');
    }
}

<?php

namespace Haida\FilamentMarketplaceConnectors\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceConnector extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'provider_key',
        'name',
        'status',
        'config',
        'metadata',
    ];

    protected $casts = [
        'config' => 'array',
        'metadata' => 'array',
    ];

    public function tokens(): HasMany
    {
        return $this->hasMany(MarketplaceToken::class, 'connector_id');
    }

    public function syncJobs(): HasMany
    {
        return $this->hasMany(MarketplaceSyncJob::class, 'connector_id');
    }

    public function getTable(): string
    {
        return config('filament-marketplace-connectors.tables.connectors', 'mkt_connectors');
    }
}

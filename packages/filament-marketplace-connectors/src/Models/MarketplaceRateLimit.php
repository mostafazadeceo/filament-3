<?php

namespace Haida\FilamentMarketplaceConnectors\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceRateLimit extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'connector_id',
        'bucket',
        'limit',
        'remaining',
        'reset_at',
    ];

    protected $casts = [
        'reset_at' => 'datetime',
    ];

    public function connector(): BelongsTo
    {
        return $this->belongsTo(MarketplaceConnector::class, 'connector_id');
    }

    public function getTable(): string
    {
        return config('filament-marketplace-connectors.tables.rate_limits', 'mkt_rate_limits');
    }
}

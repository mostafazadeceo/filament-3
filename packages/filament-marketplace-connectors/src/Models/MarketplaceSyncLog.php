<?php

namespace Haida\FilamentMarketplaceConnectors\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceSyncLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'connector_id',
        'job_type',
        'status',
        'message',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function connector(): BelongsTo
    {
        return $this->belongsTo(MarketplaceConnector::class, 'connector_id');
    }

    public function getTable(): string
    {
        return config('filament-marketplace-connectors.tables.sync_logs', 'mkt_sync_logs');
    }
}

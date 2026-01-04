<?php

namespace Haida\FilamentMarketplaceConnectors\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceSyncJob extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'connector_id',
        'job_type',
        'status',
        'last_run_at',
        'error',
        'metadata',
    ];

    protected $casts = [
        'last_run_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function connector(): BelongsTo
    {
        return $this->belongsTo(MarketplaceConnector::class, 'connector_id');
    }

    public function getTable(): string
    {
        return config('filament-marketplace-connectors.tables.sync_jobs', 'mkt_sync_jobs');
    }
}

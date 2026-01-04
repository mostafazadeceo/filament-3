<?php

namespace Haida\FilamentMarketplaceConnectors\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceToken extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'connector_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'scopes',
        'metadata',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'metadata' => 'array',
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];

    public function connector(): BelongsTo
    {
        return $this->belongsTo(MarketplaceConnector::class, 'connector_id');
    }

    public function getTable(): string
    {
        return config('filament-marketplace-connectors.tables.tokens', 'mkt_tokens');
    }
}

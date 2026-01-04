<?php

namespace Haida\FilamentThreeCx\Models;

use Haida\FilamentThreeCx\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThreeCxInstance extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'base_url',
        'verify_tls',
        'last_health_at',
        'last_error',
        'last_version',
        'last_capabilities_json',
        'client_id',
        'client_secret',
        'crm_connector_key',
        'crm_connector_key_hash',
        'xapi_enabled',
        'call_control_enabled',
        'crm_connector_enabled',
        'route_point_dn',
        'monitored_dns',
    ];

    protected $casts = [
        'verify_tls' => 'boolean',
        'last_health_at' => 'datetime',
        'last_capabilities_json' => 'array',
        'client_id' => 'encrypted',
        'client_secret' => 'encrypted',
        'crm_connector_key' => 'encrypted',
        'crm_connector_key_hash' => 'string',
        'xapi_enabled' => 'boolean',
        'call_control_enabled' => 'boolean',
        'crm_connector_enabled' => 'boolean',
        'monitored_dns' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-threecx.tables.instances', 'threecx_instances');
    }

    public function tokenCaches(): HasMany
    {
        return $this->hasMany(ThreeCxTokenCache::class, 'instance_id');
    }

    public function syncCursors(): HasMany
    {
        return $this->hasMany(ThreeCxSyncCursor::class, 'instance_id');
    }

    public function callLogs(): HasMany
    {
        return $this->hasMany(ThreeCxCallLog::class, 'instance_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ThreeCxContact::class, 'instance_id');
    }

    public function apiAuditLogs(): HasMany
    {
        return $this->hasMany(ThreeCxApiAuditLog::class, 'instance_id');
    }
}

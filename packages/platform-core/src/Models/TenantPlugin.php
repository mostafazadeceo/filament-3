<?php

namespace Haida\PlatformCore\Models;

use Filamat\IamSuite\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantPlugin extends Model
{
    protected $guarded = [];

    protected $casts = [
        'enabled' => 'bool',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'limits' => 'array',
    ];

    public function getTable(): string
    {
        return config('platform-core.tables.tenant_plugins', 'tenant_plugins');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plugin(): BelongsTo
    {
        return $this->belongsTo(PluginRegistry::class, 'plugin_key', 'plugin_key');
    }
}

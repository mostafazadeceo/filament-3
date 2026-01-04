<?php

namespace Haida\PlatformCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PluginRegistry extends Model
{
    public const STATUS_INSTALLED = 'installed';
    public const STATUS_DISABLED = 'disabled';

    protected $guarded = [];

    protected $casts = [
        'installed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function getTable(): string
    {
        return config('platform-core.tables.plugin_registry', 'plugin_registry');
    }

    public function tenantPlugins(): HasMany
    {
        return $this->hasMany(TenantPlugin::class, 'plugin_key', 'plugin_key');
    }
}

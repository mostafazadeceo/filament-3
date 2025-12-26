<?php

namespace Haida\FilamentRelograde\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RelogradeConnection extends Model
{
    protected $table = 'relograde_connections';

    protected $fillable = [
        'name',
        'environment',
        'api_key',
        'api_version',
        'base_url',
        'webhook_secret',
        'webhook_allowed_ips',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'webhook_secret' => 'encrypted',
            'webhook_allowed_ips' => 'array',
            'is_default' => 'boolean',
        ];
    }

    public function brands(): HasMany
    {
        return $this->hasMany(RelogradeBrand::class, 'connection_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(RelogradeProduct::class, 'connection_id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(RelogradeAccount::class, 'connection_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(RelogradeOrder::class, 'connection_id');
    }

    public function webhookEvents(): HasMany
    {
        return $this->hasMany(RelogradeWebhookEvent::class, 'connection_id');
    }

    public function apiLogs(): HasMany
    {
        return $this->hasMany(RelogradeApiLog::class, 'connection_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(RelogradeAlert::class, 'connection_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(RelogradeAuditLog::class, 'connection_id');
    }

    public function scopeDefault(Builder $query, ?string $environment = null): Builder
    {
        $query->where('is_default', true);

        if ($environment !== null) {
            $query->where('environment', $environment);
        }

        return $query;
    }

    public function allowedWebhookIps(): array
    {
        return $this->webhook_allowed_ips
            ?: (array) config('relograde.webhooks.allowed_ips', ['18.195.134.217']);
    }

    public function resolvedBaseUrl(): string
    {
        return $this->base_url ?: config('relograde.base_url');
    }

    public function resolvedApiVersion(): string
    {
        return $this->api_version ?: (string) config('relograde.api_version', '1.02');
    }
}

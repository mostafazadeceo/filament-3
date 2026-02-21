<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatConnection extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'provider',
        'base_url',
        'status',
        'auth_type',
        'api_user_id',
        'api_token',
        'oidc_issuer',
        'oidc_client_id',
        'oidc_client_secret',
        'oidc_scopes',
        'settings',
        'last_tested_at',
        'last_sync_at',
        'last_error_message',
        'last_error_at',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'api_token' => 'encrypted',
        'oidc_client_secret' => 'encrypted',
        'settings' => 'array',
        'last_tested_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'last_error_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('filament-chat.tables.connections', 'chat_connections');
    }

    protected static function booted(): void
    {
        static::creating(function (self $connection): void {
            if (! $connection->oidc_client_id) {
                $connection->oidc_client_id = (string) Str::uuid();
            }

            if (! $connection->oidc_client_secret) {
                $connection->oidc_client_secret = Str::random(64);
            }

            if (! $connection->oidc_scopes) {
                $scopes = config('filamat-iam.sso.oidc.allowed_scopes', 'openid profile email');
                if (is_array($scopes)) {
                    $scopes = implode(' ', $scopes);
                }
                $connection->oidc_scopes = (string) $scopes;
            }

            if (! $connection->oidc_issuer) {
                $connection->oidc_issuer = (string) config('filamat-iam.sso.oidc.issuer', '');
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->active()->orderByDesc('id');
    }
}

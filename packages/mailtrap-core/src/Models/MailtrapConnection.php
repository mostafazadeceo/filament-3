<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MailtrapConnection extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'api_token',
        'send_api_token',
        'account_id',
        'default_inbox_id',
        'status',
        'last_tested_at',
        'last_sync_at',
        'metadata',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'api_token' => 'encrypted',
        'send_api_token' => 'encrypted',
        'metadata' => 'array',
        'last_tested_at' => 'datetime',
        'last_sync_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('mailtrap-core.tables.connections', 'mailtrap_connections');
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

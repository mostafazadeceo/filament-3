<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EsimGoConnection extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'api_key',
        'status',
        'last_tested_at',
        'metadata',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_tested_at' => 'datetime',
        'api_key' => 'encrypted',
    ];

    public function getTable(): string
    {
        return config('providers-esim-go-core.tables.connections', 'esim_go_connections');
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

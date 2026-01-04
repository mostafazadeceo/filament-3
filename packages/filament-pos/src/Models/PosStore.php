<?php

namespace Haida\FilamentPos\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosStore extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'status',
        'currency',
        'timezone',
        'address',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function registers(): HasMany
    {
        return $this->hasMany(PosRegister::class, 'store_id');
    }

    public function getTable(): string
    {
        return config('filament-pos.tables.stores', 'pos_stores');
    }
}

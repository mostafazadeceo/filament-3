<?php

namespace Haida\FilamentThreeCx\Models;

use Haida\FilamentThreeCx\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreeCxTokenCache extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'instance_id',
        'scope',
        'access_token',
        'expires_at',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'expires_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('filament-threecx.tables.token_caches', 'threecx_token_caches');
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(ThreeCxInstance::class, 'instance_id');
    }
}

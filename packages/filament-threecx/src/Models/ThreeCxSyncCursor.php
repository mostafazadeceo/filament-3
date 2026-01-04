<?php

namespace Haida\FilamentThreeCx\Models;

use Haida\FilamentThreeCx\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreeCxSyncCursor extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'instance_id',
        'entity',
        'cursor',
        'last_synced_at',
    ];

    protected $casts = [
        'cursor' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('filament-threecx.tables.sync_cursors', 'threecx_sync_cursors');
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(ThreeCxInstance::class, 'instance_id');
    }
}

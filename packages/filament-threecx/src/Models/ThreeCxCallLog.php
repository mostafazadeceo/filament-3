<?php

namespace Haida\FilamentThreeCx\Models;

use Haida\FilamentThreeCx\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreeCxCallLog extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'instance_id',
        'direction',
        'from_number',
        'to_number',
        'started_at',
        'ended_at',
        'duration',
        'status',
        'external_id',
        'raw_payload',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration' => 'integer',
        'raw_payload' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-threecx.tables.call_logs', 'threecx_call_logs');
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(ThreeCxInstance::class, 'instance_id');
    }
}

<?php

namespace Haida\FilamentThreeCx\Models;

use Haida\FilamentThreeCx\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreeCxApiAuditLog extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'instance_id',
        'actor_type',
        'actor_id',
        'api_area',
        'method',
        'path',
        'status_code',
        'duration_ms',
        'correlation_id',
        'metadata',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'duration_ms' => 'integer',
        'metadata' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-threecx.tables.api_audit_logs', 'threecx_api_audit_logs');
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(ThreeCxInstance::class, 'instance_id');
    }
}

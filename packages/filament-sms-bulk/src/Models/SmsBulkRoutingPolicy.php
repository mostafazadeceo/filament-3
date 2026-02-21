<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsBulkRoutingPolicy extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_routing_policies';

    protected $fillable = [
        'tenant_id',
        'primary_connection_id',
        'fallback_connection_id',
        'enabled',
        'meta',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'meta' => 'array',
    ];

    public function primaryConnection(): BelongsTo
    {
        return $this->belongsTo(SmsBulkProviderConnection::class, 'primary_connection_id');
    }

    public function fallbackConnection(): BelongsTo
    {
        return $this->belongsTo(SmsBulkProviderConnection::class, 'fallback_connection_id');
    }
}

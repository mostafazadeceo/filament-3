<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsBulkPatternTemplate extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_pattern_templates';

    protected $fillable = [
        'tenant_id',
        'provider_connection_id',
        'pattern_code',
        'title_translations',
        'variables_schema',
        'status',
        'last_synced_at',
    ];

    protected $casts = [
        'title_translations' => 'array',
        'variables_schema' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public function providerConnection(): BelongsTo
    {
        return $this->belongsTo(SmsBulkProviderConnection::class, 'provider_connection_id');
    }
}

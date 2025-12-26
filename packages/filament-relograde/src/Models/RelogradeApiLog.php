<?php

namespace Haida\FilamentRelograde\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelogradeApiLog extends Model
{
    protected $table = 'relograde_api_logs';

    protected $fillable = [
        'connection_id',
        'method',
        'url',
        'endpoint_name',
        'request_headers',
        'request_body',
        'response_status',
        'response_body',
        'duration_ms',
        'error',
    ];

    protected function casts(): array
    {
        return [
            'request_headers' => 'array',
            'request_body' => 'array',
            'response_body' => 'array',
        ];
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(RelogradeConnection::class, 'connection_id');
    }
}

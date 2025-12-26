<?php

namespace Haida\FilamentRelograde\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelogradeWebhookEvent extends Model
{
    protected $table = 'relograde_webhook_events';

    protected $fillable = [
        'connection_id',
        'event',
        'state',
        'api_key_description',
        'trx',
        'reference',
        'payload',
        'received_ip',
        'processed_at',
        'processing_status',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(RelogradeConnection::class, 'connection_id');
    }
}

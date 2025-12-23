<?php

namespace Haida\FilamentNotify\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryLog extends Model
{
    protected $table = 'fn_delivery_logs';

    protected $fillable = [
        'panel_id',
        'rule_id',
        'trigger_key',
        'channel',
        'recipient',
        'status',
        'request_payload',
        'response_payload',
        'error',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(NotificationRule::class, 'rule_id');
    }
}

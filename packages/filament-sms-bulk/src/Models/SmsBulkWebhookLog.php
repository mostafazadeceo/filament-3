<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsBulkWebhookLog extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_webhook_logs';

    protected $fillable = [
        'tenant_id',
        'event',
        'payload',
        'signature_valid',
        'processed_at',
        'status',
    ];

    protected $casts = [
        'payload' => 'array',
        'signature_valid' => 'boolean',
        'processed_at' => 'datetime',
    ];
}

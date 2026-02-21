<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsBulkCampaignRecipient extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_campaign_recipients';

    protected $fillable = [
        'tenant_id',
        'campaign_id',
        'msisdn',
        'variables',
        'remote_message_id',
        'status',
        'delivered_at',
        'parts_count',
        'cost',
        'error_code',
        'error_message',
        'meta',
    ];

    protected $casts = [
        'variables' => 'array',
        'delivered_at' => 'datetime',
        'cost' => 'decimal:4',
        'meta' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(SmsBulkCampaign::class, 'campaign_id');
    }
}

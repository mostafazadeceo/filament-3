<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmsBulkCampaign extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_campaigns';

    protected $fillable = [
        'tenant_id',
        'provider_connection_id',
        'name',
        'mode',
        'language',
        'encoding',
        'sender',
        'cost_center',
        'schedule_at',
        'quiet_hours_profile_id',
        'approval_state',
        'approved_by',
        'approved_at',
        'cost_estimate',
        'cost_final',
        'pricing_snapshot',
        'payload_snapshot',
        'idempotency_key',
        'status',
        'started_at',
        'completed_at',
        'meta',
    ];

    protected $casts = [
        'schedule_at' => 'datetime',
        'approved_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cost_estimate' => 'decimal:4',
        'cost_final' => 'decimal:4',
        'pricing_snapshot' => 'array',
        'payload_snapshot' => 'array',
        'meta' => 'array',
    ];

    public function providerConnection(): BelongsTo
    {
        return $this->belongsTo(SmsBulkProviderConnection::class, 'provider_connection_id');
    }

    public function quietHoursProfile(): BelongsTo
    {
        return $this->belongsTo(SmsBulkQuietHoursProfile::class, 'quiet_hours_profile_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(SmsBulkCampaignRecipient::class, 'campaign_id');
    }
}

<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MailtrapCampaign extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'connection_id',
        'audience_id',
        'name',
        'subject',
        'from_email',
        'from_name',
        'html_body',
        'text_body',
        'status',
        'scheduled_at',
        'started_at',
        'finished_at',
        'stats',
        'settings',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'stats' => 'array',
        'settings' => 'array',
    ];

    public function connection(): BelongsTo
    {
        return $this->belongsTo(MailtrapConnection::class, 'connection_id');
    }

    public function audience(): BelongsTo
    {
        return $this->belongsTo(MailtrapAudience::class, 'audience_id');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(MailtrapCampaignSend::class, 'campaign_id');
    }

    public function getTable(): string
    {
        return config('mailtrap-core.tables.campaigns', 'mailtrap_campaigns');
    }
}

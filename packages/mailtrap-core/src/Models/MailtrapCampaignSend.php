<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailtrapCampaignSend extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'campaign_id',
        'audience_contact_id',
        'email',
        'name',
        'status',
        'provider_message_id',
        'error_message',
        'sent_at',
        'response',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'response' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MailtrapCampaign::class, 'campaign_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(MailtrapAudienceContact::class, 'audience_contact_id');
    }

    public function getTable(): string
    {
        return config('mailtrap-core.tables.campaign_sends', 'mailtrap_campaign_sends');
    }
}

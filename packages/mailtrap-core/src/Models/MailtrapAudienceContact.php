<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailtrapAudienceContact extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'audience_id',
        'email',
        'name',
        'status',
        'unsubscribed_at',
        'metadata',
    ];

    protected $casts = [
        'unsubscribed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function audience(): BelongsTo
    {
        return $this->belongsTo(MailtrapAudience::class, 'audience_id');
    }

    public function getTable(): string
    {
        return config('mailtrap-core.tables.audience_contacts', 'mailtrap_audience_contacts');
    }
}

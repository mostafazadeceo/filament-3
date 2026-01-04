<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Models;

use Haida\FilamentMailOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailOutboundMessage extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'domain_id',
        'mailbox_id',
        'from_email',
        'to_emails',
        'cc_emails',
        'bcc_emails',
        'subject',
        'html_body',
        'text_body',
        'status',
        'error_message',
        'provider_message_id',
        'sent_at',
        'metadata',
    ];

    protected $casts = [
        'to_emails' => 'array',
        'cc_emails' => 'array',
        'bcc_emails' => 'array',
        'metadata' => 'array',
        'sent_at' => 'datetime',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(MailDomain::class, 'domain_id');
    }

    public function mailbox(): BelongsTo
    {
        return $this->belongsTo(MailMailbox::class, 'mailbox_id');
    }

    public function getTable(): string
    {
        return config('filament-mailops.tables.outbound_messages', 'mailops_outbound_messages');
    }
}

<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Models;

use Haida\FilamentMailOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailInboundMessage extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'domain_id',
        'mailbox_id',
        'message_uid',
        'message_id',
        'subject',
        'from_email',
        'to_emails',
        'cc_emails',
        'bcc_emails',
        'received_at',
        'size',
        'is_seen',
        'html_body',
        'text_body',
        'raw_headers',
        'metadata',
        'synced_at',
    ];

    protected $casts = [
        'to_emails' => 'array',
        'cc_emails' => 'array',
        'bcc_emails' => 'array',
        'raw_headers' => 'array',
        'metadata' => 'array',
        'received_at' => 'datetime',
        'synced_at' => 'datetime',
        'is_seen' => 'bool',
        'size' => 'integer',
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
        return config('filament-mailops.tables.inbound_messages', 'mailops_inbound_messages');
    }
}

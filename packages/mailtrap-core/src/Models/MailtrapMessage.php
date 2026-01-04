<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailtrapMessage extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'connection_id',
        'inbox_id',
        'message_id',
        'subject',
        'from_email',
        'to_email',
        'sent_at',
        'size',
        'is_read',
        'attachments_count',
        'html_body',
        'text_body',
        'raw',
        'metadata',
        'synced_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'is_read' => 'bool',
        'raw' => 'array',
        'metadata' => 'array',
        'synced_at' => 'datetime',
    ];

    public function connection(): BelongsTo
    {
        return $this->belongsTo(MailtrapConnection::class, 'connection_id');
    }

    public function inbox(): BelongsTo
    {
        return $this->belongsTo(MailtrapInbox::class, 'inbox_id');
    }

    public function getTable(): string
    {
        return config('mailtrap-core.tables.messages', 'mailtrap_messages');
    }
}

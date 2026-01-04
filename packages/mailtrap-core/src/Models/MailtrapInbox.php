<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailtrapInbox extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'connection_id',
        'inbox_id',
        'name',
        'status',
        'username',
        'email_domain',
        'api_domain',
        'smtp_ports',
        'messages_count',
        'unread_count',
        'last_message_sent_at',
        'metadata',
        'synced_at',
    ];

    protected $casts = [
        'smtp_ports' => 'array',
        'metadata' => 'array',
        'messages_count' => 'integer',
        'unread_count' => 'integer',
        'last_message_sent_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public function connection(): BelongsTo
    {
        return $this->belongsTo(MailtrapConnection::class, 'connection_id');
    }

    public function getTable(): string
    {
        return config('mailtrap-core.tables.inboxes', 'mailtrap_inboxes');
    }
}

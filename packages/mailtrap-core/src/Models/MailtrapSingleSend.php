<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailtrapSingleSend extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'connection_id',
        'to_email',
        'to_name',
        'subject',
        'html_body',
        'text_body',
        'status',
        'error_message',
        'response',
        'sent_at',
        'created_by_user_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'response' => 'array',
    ];

    public function connection(): BelongsTo
    {
        return $this->belongsTo(MailtrapConnection::class, 'connection_id');
    }

    public function getTable(): string
    {
        return config('mailtrap-core.tables.single_sends', 'mailtrap_single_sends');
    }
}

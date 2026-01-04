<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppSupportMessage extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    public function getTable(): string
    {
        return (string) config('filament-app-api.tables.support_messages', 'app_support_messages');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(AppSupportTicket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }
}

<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppSupportTicket extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'latest_message_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return (string) config('filament-app-api.tables.support_tickets', 'app_support_tickets');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AppSupportMessage::class, 'ticket_id');
    }
}

<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Models;

use App\Models\User;
use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatUserLink extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'chat_connection_id',
        'user_id',
        'chat_user_id',
        'username',
        'status',
        'synced_at',
        'last_error_message',
        'last_error_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'synced_at' => 'datetime',
        'last_error_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('filament-chat.tables.user_links', 'chat_user_links');
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(ChatConnection::class, 'chat_connection_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

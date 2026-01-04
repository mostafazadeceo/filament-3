<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use BelongsToTenant;

    protected $table = 'iam_user_sessions';

    protected $guarded = [];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'revoked_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'revoked_by_id');
    }
}

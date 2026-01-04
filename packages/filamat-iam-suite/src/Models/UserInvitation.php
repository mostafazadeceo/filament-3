<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInvitation extends Model
{
    use BelongsToTenant;

    protected $table = 'iam_user_invitations';

    protected $guarded = [];

    protected $hidden = [
        'token_hash',
    ];

    protected $casts = [
        'roles' => 'array',
        'permissions' => 'array',
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'invited_by_id');
    }
}

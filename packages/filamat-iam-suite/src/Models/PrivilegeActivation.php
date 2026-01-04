<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role;

class PrivilegeActivation extends Model
{
    use BelongsToTenant;

    protected $table = 'iam_privilege_activations';

    protected $guarded = [];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(PrivilegeRequest::class, 'request_id');
    }

    public function activatedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'activated_by_id');
    }

    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'revoked_by_id');
    }
}

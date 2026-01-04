<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role;

class PrivilegeRequest extends Model
{
    use BelongsToTenant;

    protected $table = 'iam_privilege_requests';

    protected $guarded = [];

    protected $casts = [
        'request_expires_at' => 'datetime',
        'decided_at' => 'datetime',
        'requires_mfa' => 'boolean',
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

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'requested_by_id');
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'decided_by_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(PrivilegeRequestApproval::class, 'request_id');
    }
}

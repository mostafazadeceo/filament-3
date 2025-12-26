<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccessRequest extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'requested_permissions' => 'array',
        'requested_roles' => 'array',
        'meta' => 'array',
        'access_expires_at' => 'datetime',
        'request_expires_at' => 'datetime',
        'decided_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
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
        return $this->hasMany(AccessRequestApproval::class);
    }
}

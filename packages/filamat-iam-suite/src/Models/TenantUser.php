<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantUser extends Model
{
    protected $table = 'tenant_user';

    protected $guarded = [];

    protected $casts = [
        'joined_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_logout_at' => 'datetime',
        'invited_at' => 'datetime',
        'activated_at' => 'datetime',
        'suspended_at' => 'datetime',
        'security_flags' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'invited_by_id');
    }

    public function activatedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'activated_by_id');
    }

    public function suspendedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'suspended_by_id');
    }
}

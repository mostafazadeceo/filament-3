<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Events\TenantCreated;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tenant extends Model
{
    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
    ];

    protected static function booted(): void
    {
        static::created(function (Tenant $tenant) {
            event(new TenantCreated($tenant));
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'owner_user_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(config('auth.providers.users.model'), 'tenant_user')
            ->withPivot(['role', 'status', 'joined_at', 'last_login_at', 'last_logout_at', 'login_attempts', 'security_flags'])
            ->withTimestamps();
    }

    public function getTenant(): ?self
    {
        return TenantContext::getTenant();
    }
}

<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImpersonationSession extends Model
{
    use BelongsToTenant;

    protected $table = 'iam_impersonation_sessions';

    protected $guarded = [];

    protected $hidden = [
        'token_hash',
    ];

    protected $casts = [
        'restricted' => 'boolean',
        'can_write' => 'boolean',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function impersonator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'impersonator_id');
    }

    public function impersonated(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'impersonated_id');
    }

    public function endedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'ended_by_id');
    }
}

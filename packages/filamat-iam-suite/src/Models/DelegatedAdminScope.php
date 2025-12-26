<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DelegatedAdminScope extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'resource_scopes' => 'array',
        'data_scopes' => 'array',
        'action_scopes' => 'array',
        'conditions' => 'array',
        'expires_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }
}

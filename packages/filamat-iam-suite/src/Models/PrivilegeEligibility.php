<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role;

class PrivilegeEligibility extends Model
{
    use BelongsToTenant;

    protected $table = 'iam_privilege_eligibilities';

    protected $guarded = [];

    protected $casts = [
        'can_request' => 'boolean',
        'active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function eligibleBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'eligible_by_id');
    }
}

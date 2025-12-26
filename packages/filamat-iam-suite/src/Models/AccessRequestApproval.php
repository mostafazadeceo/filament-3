<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessRequestApproval extends Model
{
    protected $guarded = [];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AccessRequest::class, 'access_request_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'approver_id');
    }
}

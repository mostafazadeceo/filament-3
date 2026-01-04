<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyAuditEvent extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_audit_events';

    protected $fillable = [
        'tenant_id',
        'actor_id',
        'actor_type',
        'action',
        'subject_type',
        'subject_id',
        'ip_hash',
        'meta',
        'occurred_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'actor_id');
    }
}

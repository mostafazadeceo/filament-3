<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPointsRule extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_points_rules';

    protected $fillable = [
        'tenant_id',
        'name',
        'event_type',
        'status',
        'priority',
        'scope_type',
        'scope_ref',
        'points_type',
        'points_value',
        'percent_rate',
        'min_amount',
        'max_points',
        'cap_period',
        'cap_count',
        'valid_from',
        'valid_until',
        'conditions',
        'meta',
    ];

    protected $casts = [
        'priority' => 'integer',
        'points_value' => 'integer',
        'percent_rate' => 'decimal:4',
        'min_amount' => 'decimal:4',
        'max_points' => 'integer',
        'cap_count' => 'integer',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'conditions' => 'array',
        'meta' => 'array',
    ];
}

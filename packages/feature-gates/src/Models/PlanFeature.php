<?php

namespace Haida\FeatureGates\Models;

use Filamat\IamSuite\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFeature extends Model
{
    protected $guarded = [];

    protected $casts = [
        'enabled' => 'bool',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'limits' => 'array',
    ];

    public function getTable(): string
    {
        return config('feature-gates.tables.plan_features', 'plan_features');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }
}

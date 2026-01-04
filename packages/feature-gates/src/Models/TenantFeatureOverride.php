<?php

namespace Haida\FeatureGates\Models;

use Filamat\IamSuite\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantFeatureOverride extends Model
{
    protected $guarded = [];

    protected $casts = [
        'allowed' => 'bool',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'limits' => 'array',
    ];

    public function getTable(): string
    {
        return config('feature-gates.tables.tenant_feature_overrides', 'tenant_feature_overrides');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}

<?php

namespace Haida\FilamentLoyaltyClub\Models;

use Haida\FilamentLoyaltyClub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyCustomerMetric extends Model
{
    use UsesTenant;

    protected $table = 'loyalty_customer_metrics';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'last_purchase_at',
        'purchase_count',
        'monetary_total',
        'recency_days',
        'frequency_score',
        'monetary_score',
        'rfm_score',
        'meta',
    ];

    protected $casts = [
        'last_purchase_at' => 'datetime',
        'purchase_count' => 'integer',
        'monetary_total' => 'decimal:4',
        'recency_days' => 'integer',
        'frequency_score' => 'integer',
        'monetary_score' => 'integer',
        'rfm_score' => 'integer',
        'meta' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'customer_id');
    }
}

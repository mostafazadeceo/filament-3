<?php

namespace Haida\FilamentCommerceExperience\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExperienceReview extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'order_id',
        'customer_id',
        'rating',
        'title',
        'body',
        'status',
        'verified_purchase',
        'helpful_count',
        'abuse_flag',
        'created_by_user_id',
        'published_at',
        'metadata',
    ];

    protected $casts = [
        'verified_purchase' => 'bool',
        'abuse_flag' => 'bool',
        'published_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function votes(): HasMany
    {
        return $this->hasMany(ExperienceReviewVote::class, 'review_id');
    }

    public function getTable(): string
    {
        return config('filament-commerce-experience.tables.reviews', 'exp_reviews');
    }
}

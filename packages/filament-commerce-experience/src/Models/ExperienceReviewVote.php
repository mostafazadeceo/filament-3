<?php

namespace Haida\FilamentCommerceExperience\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperienceReviewVote extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'review_id',
        'user_id',
        'vote',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(ExperienceReview::class, 'review_id');
    }

    public function getTable(): string
    {
        return config('filament-commerce-experience.tables.review_votes', 'exp_review_votes');
    }
}

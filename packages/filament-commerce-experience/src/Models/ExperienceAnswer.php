<?php

namespace Haida\FilamentCommerceExperience\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperienceAnswer extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'question_id',
        'answered_by_user_id',
        'answer',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(ExperienceQuestion::class, 'question_id');
    }

    public function getTable(): string
    {
        return config('filament-commerce-experience.tables.answers', 'exp_answers');
    }
}

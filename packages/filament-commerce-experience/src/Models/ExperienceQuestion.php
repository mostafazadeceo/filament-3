<?php

namespace Haida\FilamentCommerceExperience\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExperienceQuestion extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'customer_id',
        'question',
        'status',
        'answered_at',
        'metadata',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(ExperienceAnswer::class, 'question_id');
    }

    public function getTable(): string
    {
        return config('filament-commerce-experience.tables.questions', 'exp_questions');
    }
}

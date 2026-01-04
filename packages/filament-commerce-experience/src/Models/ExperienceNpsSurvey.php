<?php

namespace Haida\FilamentCommerceExperience\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExperienceNpsSurvey extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'order_id',
        'customer_id',
        'channel',
        'status',
        'sent_at',
        'answered_at',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'answered_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function responses(): HasMany
    {
        return $this->hasMany(ExperienceNpsResponse::class, 'survey_id');
    }

    public function getTable(): string
    {
        return config('filament-commerce-experience.tables.nps_surveys', 'exp_nps_surveys');
    }
}

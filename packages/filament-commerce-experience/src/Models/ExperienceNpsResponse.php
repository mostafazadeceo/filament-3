<?php

namespace Haida\FilamentCommerceExperience\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperienceNpsResponse extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'survey_id',
        'score',
        'comment',
        'responded_at',
        'metadata',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(ExperienceNpsSurvey::class, 'survey_id');
    }

    public function getTable(): string
    {
        return config('filament-commerce-experience.tables.nps_responses', 'exp_nps_responses');
    }
}

<?php

namespace Haida\FilamentMeetings\Models;

use App\Models\User;
use Haida\FilamentMeetings\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingTemplate extends Model
{
    use UsesTenant;

    protected $table = 'meeting_templates';

    protected $fillable = [
        'tenant_id',
        'name',
        'format',
        'scope',
        'owner_id',
        'sections_enabled_json',
        'custom_prompts_json',
        'minutes_schema_json',
    ];

    protected $casts = [
        'sections_enabled_json' => 'array',
        'custom_prompts_json' => 'array',
        'minutes_schema_json' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}

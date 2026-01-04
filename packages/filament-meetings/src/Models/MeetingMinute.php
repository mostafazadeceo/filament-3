<?php

namespace Haida\FilamentMeetings\Models;

use Haida\FilamentMeetings\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingMinute extends Model
{
    use UsesTenant;

    protected $table = 'meeting_minutes';

    protected $fillable = [
        'tenant_id',
        'meeting_id',
        'overview_text',
        'keywords_json',
        'outline_json',
        'summary_markdown',
        'decisions_json',
        'risks_json',
    ];

    protected $casts = [
        'keywords_json' => 'array',
        'outline_json' => 'array',
        'decisions_json' => 'array',
        'risks_json' => 'array',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }
}

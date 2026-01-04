<?php

namespace Haida\FilamentMeetings\Models;

use Haida\FilamentMeetings\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingTranscript extends Model
{
    use UsesTenant;

    protected $table = 'meeting_transcripts';

    protected $fillable = [
        'tenant_id',
        'meeting_id',
        'source',
        'language',
        'content_longtext',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }
}

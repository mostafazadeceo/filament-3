<?php

namespace Haida\FilamentMeetings\Models;

use Haida\FilamentMeetings\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingTranscriptSegment extends Model
{
    use UsesTenant;

    protected $table = 'meeting_transcript_segments';

    protected $fillable = [
        'tenant_id',
        'meeting_id',
        't_start_sec',
        't_end_sec',
        'speaker_label',
        'text',
    ];

    protected $casts = [
        't_start_sec' => 'int',
        't_end_sec' => 'int',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }
}

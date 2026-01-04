<?php

namespace Haida\FilamentMeetings\Models;

use App\Models\User;
use Haida\FilamentMeetings\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meeting extends Model
{
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'meetings';

    protected $fillable = [
        'tenant_id',
        'title',
        'scheduled_at',
        'duration_minutes',
        'location_type',
        'location_value',
        'organizer_id',
        'status',
        'ai_enabled',
        'consent_required',
        'consent_mode',
        'consent_confirmed_at',
        'consent_confirmed_by',
        'share_minutes_mode',
        'minutes_format',
        'meta',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'int',
        'ai_enabled' => 'bool',
        'consent_required' => 'bool',
        'consent_confirmed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function consentConfirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consent_confirmed_by');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(MeetingAttendee::class, 'meeting_id');
    }

    public function agendaItems(): HasMany
    {
        return $this->hasMany(MeetingAgendaItem::class, 'meeting_id');
    }

    public function notes(): HasOne
    {
        return $this->hasOne(MeetingNote::class, 'meeting_id');
    }

    public function transcripts(): HasMany
    {
        return $this->hasMany(MeetingTranscript::class, 'meeting_id');
    }

    public function transcriptSegments(): HasMany
    {
        return $this->hasMany(MeetingTranscriptSegment::class, 'meeting_id');
    }

    public function minutes(): HasMany
    {
        return $this->hasMany(MeetingMinute::class, 'meeting_id');
    }

    public function actionItems(): HasMany
    {
        return $this->hasMany(MeetingActionItem::class, 'meeting_id');
    }

    public function aiRuns(): HasMany
    {
        return $this->hasMany(MeetingAiRun::class, 'meeting_id');
    }
}

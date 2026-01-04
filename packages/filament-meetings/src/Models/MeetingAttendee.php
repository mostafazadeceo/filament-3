<?php

namespace Haida\FilamentMeetings\Models;

use App\Models\User;
use Haida\FilamentMeetings\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingAttendee extends Model
{
    use UsesTenant;

    protected $table = 'meeting_attendees';

    protected $fillable = [
        'tenant_id',
        'meeting_id',
        'user_id',
        'name',
        'email_masked',
        'role',
        'invited_at',
        'responded_at',
        'attendance_status',
        'consent_granted_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'responded_at' => 'datetime',
        'consent_granted_at' => 'datetime',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

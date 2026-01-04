<?php

namespace Haida\FilamentMeetings\Models;

use App\Models\User;
use Haida\FilamentMeetings\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingAiRun extends Model
{
    use UsesTenant;

    protected $table = 'meeting_ai_runs';

    protected $fillable = [
        'tenant_id',
        'meeting_id',
        'action_type',
        'provider',
        'output_hash',
        'created_by',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

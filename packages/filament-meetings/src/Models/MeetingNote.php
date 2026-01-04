<?php

namespace Haida\FilamentMeetings\Models;

use App\Models\User;
use Haida\FilamentMeetings\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingNote extends Model
{
    use UsesTenant;

    protected $table = 'meeting_notes';

    protected $fillable = [
        'tenant_id',
        'meeting_id',
        'content_longtext',
        'updated_by',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

<?php

namespace Haida\FilamentMeetings\Models;

use App\Models\User;
use Haida\FilamentMeetings\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingAgendaItem extends Model
{
    use UsesTenant;

    protected $table = 'meeting_agenda_items';

    protected $fillable = [
        'tenant_id',
        'meeting_id',
        'sort_order',
        'title',
        'description',
        'owner_id',
        'timebox_minutes',
    ];

    protected $casts = [
        'sort_order' => 'int',
        'timebox_minutes' => 'int',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}

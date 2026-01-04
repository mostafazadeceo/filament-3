<?php

namespace Haida\FilamentMeetings\Models;

use App\Models\User;
use Haida\FilamentMeetings\Models\Concerns\UsesTenant;
use Haida\FilamentWorkhub\Models\WorkItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingActionItem extends Model
{
    use UsesTenant;

    protected $table = 'meeting_action_items';

    protected $fillable = [
        'tenant_id',
        'meeting_id',
        'title',
        'description',
        'assignee_id',
        'due_date',
        'priority',
        'status',
        'linked_workhub_item_id',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function workhubItem(): BelongsTo
    {
        return $this->belongsTo(WorkItem::class, 'linked_workhub_item_id');
    }
}

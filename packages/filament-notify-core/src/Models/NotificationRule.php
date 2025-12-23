<?php

namespace Haida\FilamentNotify\Core\Models;

use Haida\FilamentNotify\Core\Models\Concerns\HasPanelId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationRule extends Model
{
    use HasPanelId;

    protected $table = 'fn_notification_rules';

    protected $fillable = [
        'panel_id',
        'name',
        'enabled',
        'trigger_id',
        'conditions',
        'recipients',
        'channels',
        'throttle',
    ];

    protected $casts = [
        'enabled' => 'bool',
        'conditions' => 'array',
        'recipients' => 'array',
        'channels' => 'array',
        'throttle' => 'array',
    ];

    public function trigger(): BelongsTo
    {
        return $this->belongsTo(Trigger::class, 'trigger_id');
    }
}

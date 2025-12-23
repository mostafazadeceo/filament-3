<?php

namespace Haida\FilamentNotify\Core\Models;

use Haida\FilamentNotify\Core\Models\Concerns\HasPanelId;
use Illuminate\Database\Eloquent\Model;

class ChannelSetting extends Model
{
    use HasPanelId;

    protected $table = 'fn_channel_settings';

    protected $fillable = [
        'panel_id',
        'channel',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];
}

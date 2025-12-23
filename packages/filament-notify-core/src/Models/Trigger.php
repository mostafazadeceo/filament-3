<?php

namespace Haida\FilamentNotify\Core\Models;

use Haida\FilamentNotify\Core\Models\Concerns\HasPanelId;
use Illuminate\Database\Eloquent\Model;

class Trigger extends Model
{
    use HasPanelId;

    protected $table = 'fn_triggers';

    protected $fillable = [
        'panel_id',
        'key',
        'label',
        'type',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}

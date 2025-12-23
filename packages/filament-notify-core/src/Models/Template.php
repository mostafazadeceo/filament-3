<?php

namespace Haida\FilamentNotify\Core\Models;

use Haida\FilamentNotify\Core\Models\Concerns\HasPanelId;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasPanelId;

    protected $table = 'fn_templates';

    protected $fillable = [
        'panel_id',
        'name',
        'channel',
        'subject',
        'body',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}

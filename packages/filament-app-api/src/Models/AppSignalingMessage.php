<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AppSignalingMessage extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
    ];

    public function getTable(): string
    {
        return (string) config('filament-app-api.tables.signaling_messages', 'app_signaling_messages');
    }
}

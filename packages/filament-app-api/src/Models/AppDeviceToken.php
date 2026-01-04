<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppDeviceToken extends Model
{
    protected $guarded = [];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return (string) config('filament-app-api.tables.device_tokens', 'app_device_tokens');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(AppDevice::class, 'device_id');
    }
}

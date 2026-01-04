<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppDevice extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'last_seen_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return (string) config('filament-app-api.tables.devices', 'app_devices');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(AppDeviceToken::class, 'device_id');
    }
}

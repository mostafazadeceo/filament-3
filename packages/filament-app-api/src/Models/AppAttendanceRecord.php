<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppAttendanceRecord extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'clocked_at' => 'datetime',
        'meta' => 'array',
    ];

    public function getTable(): string
    {
        return (string) config('filament-app-api.tables.attendance_records', 'app_attendance_records');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }
}

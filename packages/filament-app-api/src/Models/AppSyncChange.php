<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AppSyncChange extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return (string) config('filament-app-api.tables.sync_changes', 'app_sync_changes');
    }
}

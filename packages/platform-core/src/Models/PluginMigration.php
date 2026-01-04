<?php

namespace Haida\PlatformCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PluginMigration extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'applied_at' => 'datetime',
        'triggered_by_user_id' => 'integer',
    ];

    public function getTable(): string
    {
        return config('platform-core.tables.plugin_migrations', 'plugin_migrations');
    }

    public function triggeredBy(): BelongsTo
    {
        $userModel = config('auth.providers.users.model', \App\Models\User::class);

        return $this->belongsTo($userModel, 'triggered_by_user_id');
    }
}

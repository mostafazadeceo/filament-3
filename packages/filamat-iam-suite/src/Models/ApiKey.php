<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiKey extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'abilities' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function scopes(): HasMany
    {
        return $this->hasMany(ApiKeyScope::class);
    }

    /**
     * @return array<int, string>
     */
    public function effectiveScopes(): array
    {
        $scopes = $this->relationLoaded('scopes')
            ? $this->scopes->pluck('scope')->all()
            : $this->scopes()->pluck('scope')->all();

        if ($scopes === []) {
            return $this->abilities ?? [];
        }

        return $scopes;
    }
}

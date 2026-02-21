<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Illuminate\Database\Eloquent\Model;

class OidcRefreshToken extends Model
{
    protected $table = 'iam_oidc_refresh_tokens';

    protected $fillable = [
        'client_id',
        'user_id',
        'tenant_id',
        'scope',
        'token_hash',
        'expires_at',
        'revoked_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];
}

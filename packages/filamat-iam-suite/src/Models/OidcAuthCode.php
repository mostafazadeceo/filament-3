<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Illuminate\Database\Eloquent\Model;

class OidcAuthCode extends Model
{
    protected $table = 'iam_oidc_auth_codes';

    protected $fillable = [
        'client_id',
        'user_id',
        'tenant_id',
        'redirect_uri',
        'scope',
        'nonce',
        'code_hash',
        'expires_at',
        'consumed_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];
}

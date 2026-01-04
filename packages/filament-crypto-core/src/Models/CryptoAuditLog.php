<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class CryptoAuditLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'action',
        'actor_user_id',
        'target_type',
        'target_id',
        'reason',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-crypto-core.tables.audit_logs', 'crypto_audit_logs');
    }
}

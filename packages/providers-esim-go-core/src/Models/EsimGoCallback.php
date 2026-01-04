<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class EsimGoCallback extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'event_type',
        'iccid',
        'bundle_ref',
        'remaining_quantity',
        'payload_hash',
        'raw_body',
        'payload',
        'signature_valid',
        'correlation_id',
        'received_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'signature_valid' => 'bool',
        'received_at' => 'datetime',
        'remaining_quantity' => 'decimal:4',
    ];

    public function getTable(): string
    {
        return config('providers-esim-go-core.tables.callbacks', 'esim_go_callbacks');
    }
}

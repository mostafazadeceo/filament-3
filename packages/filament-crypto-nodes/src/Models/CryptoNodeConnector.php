<?php

namespace Haida\FilamentCryptoNodes\Models;

use Filamat\IamSuite\Casts\EncryptedArray;
use Haida\FilamentCryptoCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;

class CryptoNodeConnector extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'type',
        'label',
        'status',
        'config_json',
        'last_healthy_at',
        'meta',
    ];

    protected $casts = [
        'config_json' => EncryptedArray::class,
        'last_healthy_at' => 'datetime',
        'meta' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-crypto-nodes.tables.node_connectors', 'crypto_node_connectors');
    }
}

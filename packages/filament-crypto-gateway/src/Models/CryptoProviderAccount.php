<?php

namespace Haida\FilamentCryptoGateway\Models;

use Filamat\IamSuite\Casts\EncryptedString;
use Haida\FilamentCryptoCore\Models\Concerns\UsesTenant;
use Haida\FilamentCryptoGateway\Enums\CryptoProviderEnvironment;
use Illuminate\Database\Eloquent\Model;

class CryptoProviderAccount extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'provider',
        'env',
        'merchant_id',
        'api_key_encrypted',
        'secret_encrypted',
        'config_json',
        'is_active',
    ];

    protected $casts = [
        'env' => CryptoProviderEnvironment::class,
        'api_key_encrypted' => EncryptedString::class,
        'secret_encrypted' => EncryptedString::class,
        'config_json' => 'array',
        'is_active' => 'bool',
    ];

    public function getTable(): string
    {
        return config('filament-crypto-gateway.tables.provider_accounts', 'crypto_provider_accounts');
    }
}

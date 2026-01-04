<?php

namespace Haida\FilamentCryptoGateway\Models;

use Haida\FilamentCryptoCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;

class CryptoAiReport extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'period',
        'report_at',
        'summary_md',
        'payload_json',
        'anomalies_json',
        'status',
        'meta',
    ];

    protected $casts = [
        'report_at' => 'datetime',
        'payload_json' => 'array',
        'anomalies_json' => 'array',
        'meta' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-crypto-gateway.tables.ai_reports', 'crypto_ai_reports');
    }
}

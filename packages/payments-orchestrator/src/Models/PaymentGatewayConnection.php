<?php

namespace Haida\PaymentsOrchestrator\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PaymentGatewayConnection extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'provider_key',
        'name',
        'environment',
        'api_key',
        'api_secret',
        'webhook_secret',
        'settings',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'api_secret' => 'encrypted',
            'webhook_secret' => 'encrypted',
            'settings' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getTable(): string
    {
        return config('payments-orchestrator.tables.gateway_connections', 'payment_gateway_connections');
    }
}

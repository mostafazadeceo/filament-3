<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Haida\CommerceOrders\Models\Order as CommerceOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EsimGoOrder extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'commerce_order_id',
        'connection_id',
        'provider_reference',
        'status',
        'status_message',
        'total',
        'currency',
        'raw_request',
        'raw_response',
        'correlation_id',
    ];

    protected $casts = [
        'raw_request' => 'array',
        'raw_response' => 'array',
        'total' => 'decimal:4',
    ];

    public function getTable(): string
    {
        return config('providers-esim-go-core.tables.orders', 'esim_go_orders');
    }

    public function commerceOrder(): BelongsTo
    {
        return $this->belongsTo(CommerceOrder::class, 'commerce_order_id');
    }

    public function esims(): HasMany
    {
        return $this->hasMany(EsimGoEsim::class, 'order_id');
    }
}

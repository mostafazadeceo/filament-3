<?php

namespace Haida\PaymentsOrchestrator\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Haida\CommerceOrders\Models\Order;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentIntent extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'site_id',
        'order_id',
        'provider_key',
        'status',
        'currency',
        'amount',
        'idempotency_key',
        'provider_reference',
        'redirect_url',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'meta' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function getTable(): string
    {
        return config('payments-orchestrator.tables.payment_intents', 'payment_intents');
    }
}

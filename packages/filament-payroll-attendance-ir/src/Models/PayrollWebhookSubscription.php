<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollWebhookSubscription extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_webhook_subscriptions';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'name',
        'url',
        'events',
        'secret',
        'is_active',
        'last_delivery_at',
    ];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'bool',
        'last_delivery_at' => 'datetime',
    ];

    public function deliveries(): HasMany
    {
        return $this->hasMany(PayrollWebhookDelivery::class, 'subscription_id');
    }
}

<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollWebhookDelivery extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_webhook_deliveries';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'subscription_id',
        'event',
        'payload',
        'status',
        'response_code',
        'response_body',
        'delivered_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'delivered_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(PayrollWebhookSubscription::class, 'subscription_id');
    }
}

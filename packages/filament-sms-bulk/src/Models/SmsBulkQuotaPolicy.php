<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsBulkQuotaPolicy extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_quota_policies';

    protected $fillable = [
        'tenant_id',
        'max_daily_recipients',
        'max_monthly_recipients',
        'max_daily_spend',
        'max_monthly_spend',
        'requires_approval_over_amount',
        'meta',
    ];

    protected $casts = [
        'max_daily_spend' => 'decimal:4',
        'max_monthly_spend' => 'decimal:4',
        'requires_approval_over_amount' => 'decimal:4',
        'meta' => 'array',
    ];
}

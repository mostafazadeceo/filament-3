<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsBulkRateLimitPolicy extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_rate_limit_policies';

    protected $fillable = [
        'tenant_id',
        'per_minute',
        'per_hour',
        'per_day',
        'burst',
        'provider_limits_snapshot',
    ];

    protected $casts = [
        'provider_limits_snapshot' => 'array',
    ];
}

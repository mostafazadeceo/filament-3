<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsBulkQuietHoursProfile extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_quiet_hours_profiles';

    protected $fillable = [
        'tenant_id',
        'name',
        'timezone',
        'allowed_days',
        'start_time',
        'end_time',
        'holidays',
    ];

    protected $casts = [
        'allowed_days' => 'array',
        'holidays' => 'array',
    ];
}

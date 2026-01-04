<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class SensitiveAccessLog extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_sensitive_access_logs';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'actor_id',
        'subject_type',
        'subject_id',
        'reason',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];
}

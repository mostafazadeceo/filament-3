<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollAuditEvent extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_audit_events';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'actor_id',
        'event',
        'subject_type',
        'subject_id',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];
}

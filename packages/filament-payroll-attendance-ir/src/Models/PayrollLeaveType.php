<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollLeaveType extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_leave_types';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'name',
        'code',
        'type',
        'default_days_per_year',
        'requires_approval',
        'requires_document',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'default_days_per_year' => 'decimal:2',
        'requires_approval' => 'bool',
        'requires_document' => 'bool',
        'is_active' => 'bool',
        'metadata' => 'array',
    ];
}

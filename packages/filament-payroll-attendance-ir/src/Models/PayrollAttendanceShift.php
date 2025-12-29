<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollAttendanceShift extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'payroll_attendance_shifts';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'name',
        'code',
        'start_time',
        'end_time',
        'break_minutes',
        'is_night',
        'is_rotating',
        'color',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'break_minutes' => 'integer',
        'is_night' => 'bool',
        'is_rotating' => 'bool',
        'is_active' => 'bool',
        'metadata' => 'array',
    ];
}

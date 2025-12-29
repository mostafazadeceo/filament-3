<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollAttendanceRecord extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_attendance_records';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'employee_id',
        'shift_id',
        'work_date',
        'scheduled_in',
        'scheduled_out',
        'actual_in',
        'actual_out',
        'worked_minutes',
        'late_minutes',
        'early_leave_minutes',
        'overtime_minutes',
        'night_minutes',
        'friday_minutes',
        'holiday_minutes',
        'absence_minutes',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'work_date' => 'date',
        'scheduled_in' => 'datetime',
        'scheduled_out' => 'datetime',
        'actual_in' => 'datetime',
        'actual_out' => 'datetime',
        'worked_minutes' => 'integer',
        'late_minutes' => 'integer',
        'early_leave_minutes' => 'integer',
        'overtime_minutes' => 'integer',
        'night_minutes' => 'integer',
        'friday_minutes' => 'integer',
        'holiday_minutes' => 'integer',
        'absence_minutes' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(PayrollAttendanceShift::class, 'shift_id');
    }
}

<?php

namespace Haida\FilamentPayrollAttendance\Models;

use App\Models\User;
use Haida\FilamentPayrollAttendance\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class AttendanceRecord extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_ir_attendance_records';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'employee_id',
        'shift_id',
        'date',
        'scheduled_start',
        'scheduled_end',
        'actual_start',
        'actual_end',
        'work_minutes',
        'overtime_minutes',
        'night_minutes',
        'friday_minutes',
        'holiday_minutes',
        'late_minutes',
        'early_minutes',
        'absence_minutes',
        'status',
        'approved_by',
        'metadata',
    ];

    protected $casts = [
        'date' => 'date',
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
        'work_minutes' => 'integer',
        'overtime_minutes' => 'integer',
        'night_minutes' => 'integer',
        'friday_minutes' => 'integer',
        'holiday_minutes' => 'integer',
        'late_minutes' => 'integer',
        'early_minutes' => 'integer',
        'absence_minutes' => 'integer',
        'metadata' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(AttendanceShift::class, 'shift_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(AccountingBranch::class, 'branch_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

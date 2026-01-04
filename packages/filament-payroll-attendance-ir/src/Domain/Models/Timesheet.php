<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\TimesheetPeriodType;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\TimesheetStatus;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class Timesheet extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_timesheets';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'employee_id',
        'period_start',
        'period_end',
        'period_type',
        'status',
        'worked_minutes',
        'overtime_minutes',
        'night_minutes',
        'friday_minutes',
        'holiday_minutes',
        'late_minutes',
        'early_leave_minutes',
        'absence_minutes',
        'approved_by',
        'approved_at',
        'metadata',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'period_type' => TimesheetPeriodType::class,
        'status' => TimesheetStatus::class,
        'worked_minutes' => 'integer',
        'overtime_minutes' => 'integer',
        'night_minutes' => 'integer',
        'friday_minutes' => 'integer',
        'holiday_minutes' => 'integer',
        'late_minutes' => 'integer',
        'early_leave_minutes' => 'integer',
        'absence_minutes' => 'integer',
        'approved_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(AccountingBranch::class, 'branch_id');
    }
}

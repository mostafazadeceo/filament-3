<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\ExceptionSeverity;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\ExceptionStatus;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceRecord;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class AttendanceException extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_attendance_exceptions';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'employee_id',
        'attendance_record_id',
        'time_event_id',
        'timesheet_id',
        'type',
        'severity',
        'status',
        'detected_at',
        'assigned_to',
        'resolved_by',
        'resolved_at',
        'resolution_notes',
        'metadata',
    ];

    protected $casts = [
        'severity' => ExceptionSeverity::class,
        'status' => ExceptionStatus::class,
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }

    public function attendanceRecord(): BelongsTo
    {
        return $this->belongsTo(PayrollAttendanceRecord::class, 'attendance_record_id');
    }

    public function timeEvent(): BelongsTo
    {
        return $this->belongsTo(TimeEvent::class, 'time_event_id');
    }

    public function timesheet(): BelongsTo
    {
        return $this->belongsTo(Timesheet::class, 'timesheet_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
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

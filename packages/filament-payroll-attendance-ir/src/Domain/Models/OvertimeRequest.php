<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\RequestStatus;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class OvertimeRequest extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_overtime_requests';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'employee_id',
        'work_date',
        'requested_minutes',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'work_date' => 'date',
        'requested_minutes' => 'integer',
        'status' => RequestStatus::class,
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

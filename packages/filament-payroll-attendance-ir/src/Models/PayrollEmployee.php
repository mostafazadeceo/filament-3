<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollEmployee extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'payroll_employees';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'department_id',
        'position_id',
        'user_id',
        'employee_no',
        'first_name',
        'last_name',
        'national_id',
        'birth_date',
        'phone',
        'email',
        'marital_status',
        'children_count',
        'employment_date',
        'job_title',
        'status',
        'bank_name',
        'bank_account',
        'bank_sheba',
        'metadata',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'employment_date' => 'date',
        'children_count' => 'integer',
        'department_id' => 'integer',
        'position_id' => 'integer',
        'metadata' => 'array',
    ];

    public function contracts(): HasMany
    {
        return $this->hasMany(PayrollContract::class, 'employee_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(AccountingBranch::class, 'branch_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(\Vendor\FilamentPayrollAttendanceIr\Domain\Models\Department::class, 'department_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(\Vendor\FilamentPayrollAttendanceIr\Domain\Models\Position::class, 'position_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(PayrollAttendanceRecord::class, 'employee_id');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(PayrollLeaveRequest::class, 'employee_id');
    }

    public function payrollSlips(): HasMany
    {
        return $this->hasMany(PayrollSlip::class, 'employee_id');
    }
}

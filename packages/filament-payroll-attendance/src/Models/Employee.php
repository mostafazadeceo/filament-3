<?php

namespace Haida\FilamentPayrollAttendance\Models;

use Haida\FilamentPayrollAttendance\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'payroll_ir_employees';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'employee_no',
        'first_name',
        'last_name',
        'national_id',
        'birth_date',
        'hire_date',
        'employment_type',
        'status',
        'gender',
        'marital_status',
        'children_count',
        'phone',
        'email',
        'bank_iban',
        'bank_account',
        'bank_card',
        'metadata',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
        'children_count' => 'integer',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(AccountingBranch::class, 'branch_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'employee_id');
    }

    public function payrollSlips(): HasMany
    {
        return $this->hasMany(PayrollSlip::class, 'employee_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'employee_id');
    }
}

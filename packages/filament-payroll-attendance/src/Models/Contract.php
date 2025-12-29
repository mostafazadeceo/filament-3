<?php

namespace Haida\FilamentPayrollAttendance\Models;

use Haida\FilamentPayrollAttendance\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class Contract extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'payroll_ir_contracts';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'employee_id',
        'contract_type',
        'start_date',
        'end_date',
        'status',
        'base_salary',
        'salary_period',
        'working_hours_per_week',
        'working_hours_per_day',
        'overtime_allowed',
        'night_shift_allowed',
        'shift_work_type',
        'housing_allowance',
        'food_allowance',
        'spouse_allowance',
        'child_allowance',
        'seniority_allowance',
        'allowances_payload',
        'deductions_payload',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'overtime_allowed' => 'bool',
        'night_shift_allowed' => 'bool',
        'base_salary' => 'decimal:2',
        'working_hours_per_week' => 'decimal:2',
        'working_hours_per_day' => 'decimal:2',
        'allowances_payload' => 'array',
        'deductions_payload' => 'array',
        'metadata' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
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

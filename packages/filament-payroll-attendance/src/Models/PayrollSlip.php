<?php

namespace Haida\FilamentPayrollAttendance\Models;

use Haida\FilamentPayrollAttendance\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class PayrollSlip extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_ir_payroll_slips';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'payroll_run_id',
        'employee_id',
        'contract_id',
        'slip_type',
        'status',
        'gross_amount',
        'net_amount',
        'taxable_amount',
        'insurance_amount_employee',
        'insurance_amount_employer',
        'total_allowances',
        'total_deductions',
        'issued_at',
        'metadata',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'insurance_amount_employee' => 'decimal:2',
        'insurance_amount_employer' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'issued_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(AccountingBranch::class, 'branch_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class, 'payroll_slip_id');
    }
}

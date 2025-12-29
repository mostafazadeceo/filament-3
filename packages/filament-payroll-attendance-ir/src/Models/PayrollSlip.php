<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollSlip extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_slips';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'payroll_run_id',
        'employee_id',
        'scope',
        'status',
        'gross_amount',
        'deductions_amount',
        'net_amount',
        'insurance_employee_amount',
        'insurance_employer_amount',
        'tax_amount',
        'issued_at',
        'metadata',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'deductions_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'insurance_employee_amount' => 'decimal:2',
        'insurance_employer_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'issued_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class, 'payroll_slip_id');
    }
}

<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollLoan extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_loans';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'employee_id',
        'amount',
        'installment_count',
        'installment_amount',
        'start_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'installment_count' => 'integer',
        'installment_amount' => 'decimal:2',
        'start_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(PayrollLoanInstallment::class, 'loan_id');
    }
}

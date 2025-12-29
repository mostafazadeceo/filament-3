<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollLoanInstallment extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_loan_installments';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'loan_id',
        'due_date',
        'amount',
        'paid_at',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(PayrollLoan::class, 'loan_id');
    }
}

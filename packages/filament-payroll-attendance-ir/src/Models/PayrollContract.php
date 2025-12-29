<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollContract extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'payroll_contracts';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'employee_id',
        'scope',
        'status',
        'effective_from',
        'effective_to',
        'base_salary',
        'daily_hours',
        'weekly_hours',
        'monthly_hours',
        'overtime_allowed',
        'night_shift_allowed',
        'shift_type',
        'housing_allowance',
        'food_allowance',
        'child_allowance',
        'marriage_allowance',
        'seniority_allowance',
        'extra_allowances',
        'insurance_included',
        'tax_included',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'base_salary' => 'decimal:2',
        'daily_hours' => 'decimal:2',
        'weekly_hours' => 'decimal:2',
        'monthly_hours' => 'decimal:2',
        'overtime_allowed' => 'bool',
        'night_shift_allowed' => 'bool',
        'housing_allowance' => 'decimal:2',
        'food_allowance' => 'decimal:2',
        'child_allowance' => 'decimal:2',
        'marriage_allowance' => 'decimal:2',
        'seniority_allowance' => 'decimal:2',
        'extra_allowances' => 'array',
        'insurance_included' => 'bool',
        'tax_included' => 'bool',
        'metadata' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }
}

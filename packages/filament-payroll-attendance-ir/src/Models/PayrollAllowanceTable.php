<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollAllowanceTable extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_allowance_tables';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'effective_from',
        'effective_to',
        'housing_allowance',
        'food_allowance',
        'child_allowance_daily',
        'marriage_allowance',
        'seniority_allowance_daily',
        'description',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'housing_allowance' => 'decimal:2',
        'food_allowance' => 'decimal:2',
        'child_allowance_daily' => 'decimal:2',
        'marriage_allowance' => 'decimal:2',
        'seniority_allowance_daily' => 'decimal:2',
    ];
}

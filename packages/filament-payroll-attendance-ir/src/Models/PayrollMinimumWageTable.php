<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollMinimumWageTable extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_minimum_wage_tables';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'effective_from',
        'effective_to',
        'daily_wage',
        'monthly_wage',
        'description',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'daily_wage' => 'decimal:2',
        'monthly_wage' => 'decimal:2',
    ];
}

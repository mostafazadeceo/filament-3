<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollInsuranceTable extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_insurance_tables';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'effective_from',
        'effective_to',
        'employee_rate',
        'employer_rate',
        'max_insurable_daily',
        'max_insurable_monthly',
        'description',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'employee_rate' => 'decimal:2',
        'employer_rate' => 'decimal:2',
        'max_insurable_daily' => 'decimal:2',
        'max_insurable_monthly' => 'decimal:2',
    ];
}

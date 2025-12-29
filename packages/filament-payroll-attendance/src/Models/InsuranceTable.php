<?php

namespace Haida\FilamentPayrollAttendance\Models;

use Haida\FilamentPayrollAttendance\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class InsuranceTable extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_ir_insurance_tables';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'effective_from',
        'effective_to',
        'employee_rate',
        'employer_rate',
        'min_daily_wage',
        'max_daily_wage',
        'max_insurable_daily_wage',
        'metadata',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'employee_rate' => 'decimal:4',
        'employer_rate' => 'decimal:4',
        'min_daily_wage' => 'decimal:2',
        'max_daily_wage' => 'decimal:2',
        'max_insurable_daily_wage' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }
}

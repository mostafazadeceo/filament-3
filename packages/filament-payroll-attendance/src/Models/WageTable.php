<?php

namespace Haida\FilamentPayrollAttendance\Models;

use Haida\FilamentPayrollAttendance\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class WageTable extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_ir_wage_tables';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'effective_from',
        'effective_to',
        'min_daily_wage',
        'min_monthly_wage',
        'housing_allowance',
        'food_allowance',
        'spouse_allowance',
        'child_allowance',
        'seniority_daily',
        'metadata',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'min_daily_wage' => 'decimal:2',
        'min_monthly_wage' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'food_allowance' => 'decimal:2',
        'spouse_allowance' => 'decimal:2',
        'child_allowance' => 'decimal:2',
        'seniority_daily' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }
}

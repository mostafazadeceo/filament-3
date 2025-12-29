<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class PayrollRun extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_payroll_runs';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'fiscal_period_id',
        'run_date',
        'status',
        'metadata',
    ];

    protected $casts = [
        'run_date' => 'date',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function fiscalPeriod(): BelongsTo
    {
        return $this->belongsTo(FiscalPeriod::class, 'fiscal_period_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class, 'payroll_run_id');
    }

    public function slips(): HasMany
    {
        return $this->hasMany(PayrollSlip::class, 'payroll_run_id');
    }
}

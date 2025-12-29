<?php

namespace Haida\FilamentPayrollAttendance\Models;

use Haida\FilamentPayrollAttendance\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class PayrollItem extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_ir_payroll_items';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'payroll_slip_id',
        'item_type',
        'code',
        'title',
        'amount',
        'is_taxable',
        'is_insurable',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_taxable' => 'bool',
        'is_insurable' => 'bool',
        'metadata' => 'array',
    ];

    public function payrollSlip(): BelongsTo
    {
        return $this->belongsTo(PayrollSlip::class, 'payroll_slip_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }
}

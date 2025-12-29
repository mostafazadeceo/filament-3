<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollTaxBracket extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_tax_brackets';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'payroll_tax_table_id',
        'min_amount',
        'max_amount',
        'rate',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'rate' => 'decimal:2',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(PayrollTaxTable::class, 'payroll_tax_table_id');
    }
}

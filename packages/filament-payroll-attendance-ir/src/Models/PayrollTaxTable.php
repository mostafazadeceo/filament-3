<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollTaxTable extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_tax_tables';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'effective_from',
        'effective_to',
        'exemption_amount',
        'flat_allowance_rate',
        'description',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'exemption_amount' => 'decimal:2',
        'flat_allowance_rate' => 'decimal:2',
    ];

    public function brackets(): HasMany
    {
        return $this->hasMany(PayrollTaxBracket::class, 'payroll_tax_table_id');
    }
}

<?php

namespace Haida\FilamentPayrollAttendance\Models;

use Haida\FilamentPayrollAttendance\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class TaxTable extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_ir_tax_tables';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'effective_from',
        'effective_to',
        'monthly_exemption',
        'flat_extras_rate',
        'metadata',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'monthly_exemption' => 'decimal:2',
        'flat_extras_rate' => 'decimal:4',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function brackets(): HasMany
    {
        return $this->hasMany(TaxBracket::class, 'tax_table_id');
    }
}

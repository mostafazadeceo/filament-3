<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class FiscalPeriod extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_fiscal_periods';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'fiscal_year_id',
        'name',
        'start_date',
        'end_date',
        'period_type',
        'is_closed',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_closed' => 'bool',
        'metadata' => 'array',
    ];

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }
}

<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class VatPeriod extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_vat_periods';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'fiscal_year_id',
        'period_start',
        'period_end',
        'status',
        'metadata',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(VatReport::class, 'vat_period_id');
    }
}

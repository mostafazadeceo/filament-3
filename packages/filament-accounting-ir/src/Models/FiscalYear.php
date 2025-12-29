<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class FiscalYear extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_fiscal_years';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'name',
        'start_date',
        'end_date',
        'is_closed',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_closed' => 'bool',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function periods(): HasMany
    {
        return $this->hasMany(FiscalPeriod::class, 'fiscal_year_id');
    }
}

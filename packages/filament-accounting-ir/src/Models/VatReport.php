<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VatReport extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_vat_reports';

    protected $fillable = [
        'vat_period_id',
        'sales_base',
        'sales_tax',
        'purchase_base',
        'purchase_tax',
        'status',
        'metadata',
    ];

    protected $casts = [
        'sales_base' => 'decimal:2',
        'sales_tax' => 'decimal:2',
        'purchase_base' => 'decimal:2',
        'purchase_tax' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(VatPeriod::class, 'vat_period_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(VatReportLine::class, 'vat_report_id');
    }
}

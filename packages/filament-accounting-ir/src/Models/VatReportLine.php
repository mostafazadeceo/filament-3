<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VatReportLine extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_vat_report_lines';

    protected $fillable = [
        'vat_report_id',
        'source_type',
        'source_id',
        'base_amount',
        'tax_amount',
        'metadata',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(VatReport::class, 'vat_report_id');
    }
}

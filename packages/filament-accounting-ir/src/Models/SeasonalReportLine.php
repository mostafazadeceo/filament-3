<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeasonalReportLine extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_seasonal_report_lines';

    protected $fillable = [
        'seasonal_report_id',
        'party_id',
        'invoice_no',
        'invoice_date',
        'amount',
        'tax_amount',
        'metadata',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(SeasonalReport::class, 'seasonal_report_id');
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'party_id');
    }
}

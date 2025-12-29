<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeasonalSubmission extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_seasonal_submissions';

    protected $fillable = [
        'seasonal_report_id',
        'submitted_at',
        'status',
        'response_payload',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'response_payload' => 'array',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(SeasonalReport::class, 'seasonal_report_id');
    }
}

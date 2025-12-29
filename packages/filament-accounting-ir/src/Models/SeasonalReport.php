<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class SeasonalReport extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_seasonal_reports';

    protected $fillable = [
        'tenant_id',
        'company_id',
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

    public function lines(): HasMany
    {
        return $this->hasMany(SeasonalReportLine::class, 'seasonal_report_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(SeasonalSubmission::class, 'seasonal_report_id');
    }
}

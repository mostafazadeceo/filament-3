<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class HolidayRule extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_holiday_rules';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'work_calendar_id',
        'holiday_date',
        'title',
        'is_public',
        'source',
        'metadata',
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'is_public' => 'boolean',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(WorkCalendar::class, 'work_calendar_id');
    }
}

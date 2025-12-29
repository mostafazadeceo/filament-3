<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollHoliday extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_holidays';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'holiday_date',
        'title',
        'is_public',
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'is_public' => 'bool',
    ];
}

<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollRun extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_runs';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'period_start',
        'period_end',
        'status',
        'approved_by',
        'approved_at',
        'posted_at',
        'locked_at',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'approved_at' => 'datetime',
        'posted_at' => 'datetime',
        'locked_at' => 'datetime',
    ];

    public function slips(): HasMany
    {
        return $this->hasMany(PayrollSlip::class, 'payroll_run_id');
    }
}

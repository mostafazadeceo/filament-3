<?php

namespace Haida\FilamentPayrollAttendance\Models;

use Haida\FilamentPayrollAttendance\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class AttendanceShift extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'payroll_ir_shifts';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'name',
        'start_time',
        'end_time',
        'break_minutes',
        'is_overnight',
        'is_shift_work',
        'shift_work_type',
        'color',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'break_minutes' => 'integer',
        'is_overnight' => 'bool',
        'is_shift_work' => 'bool',
        'is_active' => 'bool',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(AccountingBranch::class, 'branch_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(AttendanceSchedule::class, 'shift_id');
    }
}

<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\PolicyStatus;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class AttendancePolicy extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'payroll_attendance_policies';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'name',
        'status',
        'is_default',
        'requires_consent',
        'allow_remote_work',
        'rules',
        'metadata',
    ];

    protected $casts = [
        'status' => PolicyStatus::class,
        'is_default' => 'boolean',
        'requires_consent' => 'boolean',
        'allow_remote_work' => 'boolean',
        'rules' => 'array',
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
}

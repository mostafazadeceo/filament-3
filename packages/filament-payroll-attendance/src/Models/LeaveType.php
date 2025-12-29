<?php

namespace Haida\FilamentPayrollAttendance\Models;

use Haida\FilamentPayrollAttendance\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class LeaveType extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'payroll_ir_leave_types';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'code',
        'name',
        'is_paid',
        'annual_quota_days',
        'carryover_limit_days',
        'requires_attachment',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_paid' => 'bool',
        'annual_quota_days' => 'decimal:2',
        'carryover_limit_days' => 'decimal:2',
        'requires_attachment' => 'bool',
        'is_active' => 'bool',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'leave_type_id');
    }
}

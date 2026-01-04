<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class Department extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'payroll_departments';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'parent_id',
        'manager_id',
        'name',
        'code',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(PayrollEmployee::class, 'manager_id');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'department_id');
    }
}

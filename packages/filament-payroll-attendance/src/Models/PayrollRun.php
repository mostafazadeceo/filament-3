<?php

namespace Haida\FilamentPayrollAttendance\Models;

use App\Models\User;
use Haida\FilamentPayrollAttendance\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class PayrollRun extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_ir_payroll_runs';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'period_start',
        'period_end',
        'run_date',
        'run_type',
        'status',
        'notes',
        'created_by',
        'approved_by',
        'posted_at',
        'locked_at',
        'metadata',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'run_date' => 'date',
        'posted_at' => 'datetime',
        'locked_at' => 'datetime',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function slips(): HasMany
    {
        return $this->hasMany(PayrollSlip::class, 'payroll_run_id');
    }
}

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

class Settlement extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_ir_settlements';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'employee_id',
        'settlement_date',
        'status',
        'total_amount',
        'notes',
        'approved_by',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'settlement_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(AccountingBranch::class, 'branch_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SettlementItem::class, 'settlement_id');
    }
}

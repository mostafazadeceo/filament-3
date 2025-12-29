<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'accounting_ir_employees';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'employee_no',
        'name',
        'national_id',
        'hire_date',
        'status',
        'metadata',
    ];

    protected $casts = [
        'hire_date' => 'date',
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

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class, 'employee_id');
    }

    public function payrollSlips(): HasMany
    {
        return $this->hasMany(PayrollSlip::class, 'employee_id');
    }
}

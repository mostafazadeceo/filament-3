<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollAdvance extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_advances';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'employee_id',
        'amount',
        'advance_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'advance_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }
}

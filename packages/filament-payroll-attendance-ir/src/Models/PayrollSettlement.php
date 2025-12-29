<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollSettlement extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_settlements';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'employee_id',
        'settlement_date',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'settlement_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }
}

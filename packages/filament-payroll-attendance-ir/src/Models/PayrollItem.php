<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollItem extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_items';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'payroll_slip_id',
        'code',
        'name',
        'type',
        'amount',
        'tax_method',
        'tax_rate',
        'is_insurable',
        'is_recurring',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'is_insurable' => 'bool',
        'is_recurring' => 'bool',
        'metadata' => 'array',
    ];

    public function slip(): BelongsTo
    {
        return $this->belongsTo(PayrollSlip::class, 'payroll_slip_id');
    }
}

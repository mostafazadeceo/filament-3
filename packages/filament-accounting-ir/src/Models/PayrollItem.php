<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItem extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_payroll_items';

    protected $fillable = [
        'payroll_run_id',
        'employee_id',
        'gross',
        'net',
        'tax',
        'insurance',
        'details',
    ];

    protected $casts = [
        'gross' => 'decimal:2',
        'net' => 'decimal:2',
        'tax' => 'decimal:2',
        'insurance' => 'decimal:2',
        'details' => 'array',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}

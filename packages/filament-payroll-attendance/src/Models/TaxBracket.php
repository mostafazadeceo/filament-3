<?php

namespace Haida\FilamentPayrollAttendance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxBracket extends Model
{
    use HasFactory;

    protected $table = 'payroll_ir_tax_brackets';

    protected $fillable = [
        'tax_table_id',
        'from_amount',
        'to_amount',
        'rate',
    ];

    protected $casts = [
        'from_amount' => 'decimal:2',
        'to_amount' => 'decimal:2',
        'rate' => 'decimal:4',
    ];

    public function taxTable(): BelongsTo
    {
        return $this->belongsTo(TaxTable::class, 'tax_table_id');
    }
}

<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollTable extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_payroll_tables';

    protected $fillable = [
        'table_type',
        'effective_from',
        'effective_to',
        'payload',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'payload' => 'array',
    ];
}

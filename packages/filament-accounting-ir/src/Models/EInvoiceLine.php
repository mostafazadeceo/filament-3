<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EInvoiceLine extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_e_invoice_lines';

    protected $fillable = [
        'e_invoice_id',
        'description',
        'quantity',
        'unit_price',
        'tax_amount',
        'line_total',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
        'metadata' => 'array',
    ];
}

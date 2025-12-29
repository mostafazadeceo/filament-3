<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EInvoiceStatusLog extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_e_invoice_status_logs';

    protected $fillable = [
        'e_invoice_id',
        'status',
        'message',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function eInvoice(): BelongsTo
    {
        return $this->belongsTo(EInvoice::class, 'e_invoice_id');
    }
}

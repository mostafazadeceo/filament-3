<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EInvoiceSubmission extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_e_invoice_submissions';

    protected $fillable = [
        'e_invoice_id',
        'provider_id',
        'status',
        'correlation_id',
        'request_payload',
        'response_payload',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function eInvoice(): BelongsTo
    {
        return $this->belongsTo(EInvoice::class, 'e_invoice_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(EInvoiceProvider::class, 'provider_id');
    }
}

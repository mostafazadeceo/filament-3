<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class EInvoice extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_e_invoices';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'sales_invoice_id',
        'provider_id',
        'invoice_type',
        'status',
        'unique_tax_id',
        'payload_version',
        'issued_at',
        'payload',
        'metadata',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'payload' => 'array',
        'metadata' => 'array',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(EInvoiceProvider::class, 'provider_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(EInvoiceLine::class, 'e_invoice_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(EInvoiceSubmission::class, 'e_invoice_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(EInvoiceStatusLog::class, 'e_invoice_id');
    }
}

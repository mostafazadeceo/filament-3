<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class EInvoiceProvider extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_e_invoice_providers';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'name',
        'driver',
        'is_active',
        'config',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'config' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(EInvoice::class, 'provider_id');
    }
}

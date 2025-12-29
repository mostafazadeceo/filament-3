<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class TaxCategory extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_tax_categories';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'code',
        'name',
        'vat_rate',
        'is_exempt',
        'metadata',
    ];

    protected $casts = [
        'vat_rate' => 'decimal:4',
        'is_exempt' => 'bool',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }
}

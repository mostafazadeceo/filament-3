<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class WithholdingItem extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_withholding_items';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'party_id',
        'source_type',
        'source_id',
        'base_amount',
        'tax_amount',
        'tax_date',
        'metadata',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_date' => 'date',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'party_id');
    }
}

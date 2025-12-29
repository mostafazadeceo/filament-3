<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class WithholdingRate extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_withholding_rates';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'code',
        'name',
        'rate',
        'effective_from',
        'effective_to',
        'metadata',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }
}

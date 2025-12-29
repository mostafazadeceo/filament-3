<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class TaxRate extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_tax_rates';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'code',
        'name',
        'tax_type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(TaxRateVersion::class, 'tax_rate_id');
    }
}

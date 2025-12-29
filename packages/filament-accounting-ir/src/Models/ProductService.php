<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class ProductService extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'accounting_ir_products_services';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'code',
        'name',
        'item_type',
        'uom_id',
        'tax_category_id',
        'base_price',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'bool',
        'metadata' => 'array',
    ];

    public function uom(): BelongsTo
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function taxCategory(): BelongsTo
    {
        return $this->belongsTo(TaxCategory::class, 'tax_category_id');
    }
}

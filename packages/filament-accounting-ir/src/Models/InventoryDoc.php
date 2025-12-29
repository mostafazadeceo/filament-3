<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class InventoryDoc extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_inventory_docs';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'warehouse_id',
        'doc_type',
        'doc_no',
        'doc_date',
        'status',
        'description',
        'metadata',
    ];

    protected $casts = [
        'doc_date' => 'date',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InventoryDocLine::class, 'inventory_doc_id');
    }
}

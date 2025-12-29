<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class InventoryWarehouse extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_inventory_warehouses';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'name',
        'code',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(AccountingBranch::class, 'branch_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(InventoryLocation::class, 'warehouse_id');
    }

    public function docs(): HasMany
    {
        return $this->hasMany(InventoryDoc::class, 'warehouse_id');
    }
}

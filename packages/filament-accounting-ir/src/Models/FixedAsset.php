<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class FixedAsset extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'accounting_ir_fixed_assets';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'name',
        'asset_code',
        'category',
        'acquisition_date',
        'cost',
        'salvage_value',
        'depreciation_method',
        'useful_life_months',
        'status',
        'metadata',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
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

    public function depreciationSchedules(): HasMany
    {
        return $this->hasMany(DepreciationSchedule::class, 'fixed_asset_id');
    }

    public function depreciationEntries(): HasMany
    {
        return $this->hasMany(DepreciationEntry::class, 'fixed_asset_id');
    }
}

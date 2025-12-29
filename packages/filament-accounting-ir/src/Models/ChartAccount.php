<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class ChartAccount extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'accounting_ir_chart_accounts';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'plan_id',
        'type_id',
        'parent_id',
        'code',
        'name',
        'level',
        'is_postable',
        'requires_dimensions',
        'is_active',
        'sort_order',
        'metadata',
    ];

    protected $casts = [
        'is_postable' => 'bool',
        'requires_dimensions' => 'array',
        'is_active' => 'bool',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(AccountPlan::class, 'plan_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'type_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}

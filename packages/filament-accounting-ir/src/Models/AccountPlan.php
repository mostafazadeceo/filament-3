<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class AccountPlan extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_account_plans';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'name',
        'industry',
        'is_default',
        'metadata',
    ];

    protected $casts = [
        'is_default' => 'bool',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(ChartAccount::class, 'plan_id');
    }
}

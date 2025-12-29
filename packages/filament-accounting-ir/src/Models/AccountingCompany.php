<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class AccountingCompany extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'accounting_ir_companies';

    protected $fillable = [
        'tenant_id',
        'name',
        'legal_name',
        'national_id',
        'economic_code',
        'registration_number',
        'vat_number',
        'timezone',
        'base_currency',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'metadata' => 'array',
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(AccountingBranch::class, 'company_id');
    }

    public function fiscalYears(): HasMany
    {
        return $this->hasMany(FiscalYear::class, 'company_id');
    }

    public function setting(): HasOne
    {
        return $this->hasOne(AccountingCompanySetting::class, 'company_id');
    }
}

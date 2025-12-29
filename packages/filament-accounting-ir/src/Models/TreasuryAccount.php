<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class TreasuryAccount extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_treasury_accounts';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'account_type',
        'name',
        'account_no',
        'iban',
        'bank_name',
        'currency',
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

    public function transactions(): HasMany
    {
        return $this->hasMany(TreasuryTransaction::class, 'treasury_account_id');
    }

    public function cheques(): HasMany
    {
        return $this->hasMany(Cheque::class, 'treasury_account_id');
    }
}

<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class AccountingCompanySetting extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_company_settings';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'posting_accounts',
        'posting_requires_approval',
        'allow_negative_inventory',
        'metadata',
    ];

    protected $casts = [
        'posting_accounts' => 'array',
        'posting_requires_approval' => 'bool',
        'allow_negative_inventory' => 'bool',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }
}

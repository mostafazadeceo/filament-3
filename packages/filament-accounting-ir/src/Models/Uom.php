<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class Uom extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_uoms';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'code',
        'name',
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
}

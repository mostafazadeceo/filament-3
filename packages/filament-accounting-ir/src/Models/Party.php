<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class Party extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'accounting_ir_parties';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'party_type',
        'name',
        'legal_name',
        'national_id',
        'economic_code',
        'registration_number',
        'phone',
        'email',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'metadata' => 'array',
    ];

    public function salesInvoices(): HasMany
    {
        return $this->hasMany(SalesInvoice::class, 'party_id');
    }

    public function purchaseInvoices(): HasMany
    {
        return $this->hasMany(PurchaseInvoice::class, 'party_id');
    }
}

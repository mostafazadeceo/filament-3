<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class Contract extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_contracts';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'project_id',
        'party_id',
        'contract_no',
        'amount',
        'start_date',
        'end_date',
        'status',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'metadata' => 'array',
    ];
}

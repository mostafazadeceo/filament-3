<?php

namespace Haida\FilamentPayrollAttendance\Models;

use Haida\FilamentPayrollAttendance\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class Holiday extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_ir_holidays';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'date',
        'title',
        'is_official',
        'is_weekly',
        'metadata',
    ];

    protected $casts = [
        'date' => 'date',
        'is_official' => 'bool',
        'is_weekly' => 'bool',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }
}

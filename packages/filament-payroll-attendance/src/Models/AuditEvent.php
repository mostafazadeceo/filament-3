<?php

namespace Haida\FilamentPayrollAttendance\Models;

use App\Models\User;
use Haida\FilamentPayrollAttendance\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class AuditEvent extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_ir_audit_events';

    public $timestamps = true;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'actor_id',
        'action',
        'entity_type',
        'entity_id',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }
}

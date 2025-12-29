<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollEmployeeDocument extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_employee_documents';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'employee_id',
        'document_type',
        'path',
        'issued_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
        'metadata' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }
}

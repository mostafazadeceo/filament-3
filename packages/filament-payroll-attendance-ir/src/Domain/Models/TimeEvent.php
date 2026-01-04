<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\TimeEventType;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class TimeEvent extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_time_events';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'employee_id',
        'event_at',
        'event_type',
        'source',
        'device_ref',
        'latitude',
        'longitude',
        'wifi_ssid',
        'ip_address',
        'proof_type',
        'proof_payload',
        'is_verified',
        'verified_by',
        'verified_at',
        'metadata',
    ];

    protected $casts = [
        'event_at' => 'datetime',
        'event_type' => TimeEventType::class,
        'latitude' => 'float',
        'longitude' => 'float',
        'proof_payload' => 'array',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(AccountingBranch::class, 'branch_id');
    }
}

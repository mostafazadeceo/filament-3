<?php

namespace Vendor\FilamentPayrollAttendanceIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vendor\FilamentPayrollAttendanceIr\Models\Concerns\UsesTenant;

class PayrollAiLog extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'payroll_ai_logs';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'actor_id',
        'report_type',
        'period_start',
        'period_end',
        'provider',
        'input_hash',
        'response_summary',
        'input_payload',
        'output_payload',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'input_payload' => 'array',
        'output_payload' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];
}

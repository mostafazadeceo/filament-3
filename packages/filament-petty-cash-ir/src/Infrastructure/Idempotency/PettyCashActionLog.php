<?php

namespace Haida\FilamentPettyCashIr\Infrastructure\Idempotency;

use Haida\FilamentPettyCashIr\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;

class PettyCashActionLog extends Model
{
    use UsesTenant;

    protected $table = 'petty_cash_action_logs';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'action',
        'subject_type',
        'subject_id',
        'idempotency_key',
        'status',
        'actor_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}

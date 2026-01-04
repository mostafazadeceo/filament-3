<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IamAiActionProposal extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'target' => 'array',
        'result' => 'array',
        'requires_approval' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'executed_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(IamAiReport::class, 'report_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'approved_by_id');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'rejected_by_id');
    }

    public function executedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'executed_by_id');
    }
}

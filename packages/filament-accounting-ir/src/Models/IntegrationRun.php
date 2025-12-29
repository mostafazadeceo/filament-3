<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntegrationRun extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_integration_runs';

    protected $fillable = [
        'integration_connector_id',
        'started_at',
        'finished_at',
        'status',
        'summary',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'summary' => 'array',
    ];

    public function connector(): BelongsTo
    {
        return $this->belongsTo(IntegrationConnector::class, 'integration_connector_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(IntegrationLog::class, 'integration_run_id');
    }
}

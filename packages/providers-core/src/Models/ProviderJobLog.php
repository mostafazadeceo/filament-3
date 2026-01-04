<?php

declare(strict_types=1);

namespace Haida\ProvidersCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ProviderJobLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'provider_key',
        'job_type',
        'status',
        'connection_id',
        'attempts',
        'payload',
        'result',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'result' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('providers-core.tables.job_logs', 'providers_core_job_logs');
    }
}

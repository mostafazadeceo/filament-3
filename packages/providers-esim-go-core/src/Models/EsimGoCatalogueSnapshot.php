<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class EsimGoCatalogueSnapshot extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'fetched_at',
        'filters',
        'hash',
        'payload',
        'source_version',
    ];

    protected $casts = [
        'fetched_at' => 'datetime',
        'filters' => 'array',
        'payload' => 'array',
    ];

    public function getTable(): string
    {
        return config('providers-esim-go-core.tables.catalogue_snapshots', 'esim_go_catalogue_snapshots');
    }
}

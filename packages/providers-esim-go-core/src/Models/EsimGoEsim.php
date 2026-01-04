<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EsimGoEsim extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'order_id',
        'iccid',
        'matching_id',
        'smdp_address',
        'state',
        'first_installed_at',
        'last_refreshed_at',
        'external_ref',
    ];

    protected $casts = [
        'first_installed_at' => 'datetime',
        'last_refreshed_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('providers-esim-go-core.tables.esims', 'esim_go_esims');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(EsimGoOrder::class, 'order_id');
    }
}

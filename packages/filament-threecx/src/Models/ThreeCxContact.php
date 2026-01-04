<?php

namespace Haida\FilamentThreeCx\Models;

use Haida\FilamentThreeCx\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreeCxContact extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'instance_id',
        'name',
        'phones',
        'emails',
        'external_id',
        'crm_url',
        'raw_payload',
    ];

    protected $casts = [
        'phones' => 'array',
        'emails' => 'array',
        'raw_payload' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-threecx.tables.contacts', 'threecx_contacts');
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(ThreeCxInstance::class, 'instance_id');
    }
}

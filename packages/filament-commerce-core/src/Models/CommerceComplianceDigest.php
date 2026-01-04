<?php

namespace Haida\FilamentCommerceCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class CommerceComplianceDigest extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'period_start',
        'period_end',
        'status',
        'summary',
        'meta',
        'created_by_user_id',
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'summary' => 'array',
        'meta' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-commerce-core.tables.compliance_digests', 'commerce_compliance_digests');
    }
}

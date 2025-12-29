<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class IntegrationConnector extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_integration_connectors';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'name',
        'connector_type',
        'schedule',
        'is_active',
        'config',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'config' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AccountingCompany::class, 'company_id');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(IntegrationRun::class, 'integration_connector_id');
    }

    public function mappings(): HasMany
    {
        return $this->hasMany(IntegrationMapping::class, 'integration_connector_id');
    }
}

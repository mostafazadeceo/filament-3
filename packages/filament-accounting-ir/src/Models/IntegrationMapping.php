<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegrationMapping extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_integration_mappings';

    protected $fillable = [
        'integration_connector_id',
        'entity',
        'mapping',
    ];

    protected $casts = [
        'mapping' => 'array',
    ];

    public function connector(): BelongsTo
    {
        return $this->belongsTo(IntegrationConnector::class, 'integration_connector_id');
    }
}

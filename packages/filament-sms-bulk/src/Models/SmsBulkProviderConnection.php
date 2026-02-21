<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmsBulkProviderConnection extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_provider_connections';

    protected $fillable = [
        'tenant_id',
        'provider',
        'display_name',
        'base_url_override',
        'encrypted_token',
        'default_sender',
        'status',
        'last_tested_at',
        'last_credit_snapshot',
        'meta',
    ];

    protected $casts = [
        'encrypted_token' => 'encrypted',
        'meta' => 'array',
        'last_tested_at' => 'datetime',
        'last_credit_snapshot' => 'decimal:4',
    ];

    public function senderIdentities(): HasMany
    {
        return $this->hasMany(SmsBulkSenderIdentity::class, 'provider_connection_id');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(SmsBulkCampaign::class, 'provider_connection_id');
    }
}

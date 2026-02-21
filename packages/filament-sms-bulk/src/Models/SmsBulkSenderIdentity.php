<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsBulkSenderIdentity extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_sender_identities';

    protected $fillable = [
        'tenant_id',
        'provider_connection_id',
        'sender',
        'label',
        'status',
    ];

    public function providerConnection(): BelongsTo
    {
        return $this->belongsTo(SmsBulkProviderConnection::class, 'provider_connection_id');
    }
}

<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsBulkConsentRegistry extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_consent_registries';

    protected $fillable = [
        'tenant_id',
        'msisdn',
        'status',
        'source',
        'consented_at',
        'revoked_at',
        'meta',
    ];

    protected $casts = [
        'consented_at' => 'datetime',
        'revoked_at' => 'datetime',
        'meta' => 'array',
    ];
}

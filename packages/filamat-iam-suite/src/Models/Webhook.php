<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Casts\EncryptedArray;
use Filamat\IamSuite\Casts\EncryptedString;
use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Webhook extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'events' => 'array',
        'enabled' => 'boolean',
        'headers_static' => EncryptedArray::class,
        'redaction_policy' => 'array',
        'rate_limit' => 'array',
        'is_ai_auditor' => 'boolean',
        'secret' => EncryptedString::class,
    ];

    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }
}

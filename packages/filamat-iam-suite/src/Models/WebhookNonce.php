<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookNonce extends Model
{
    protected $guarded = [];

    protected $casts = [
        'timestamp' => 'integer',
    ];

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }
}

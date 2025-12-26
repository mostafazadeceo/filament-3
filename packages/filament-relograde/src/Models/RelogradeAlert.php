<?php

namespace Haida\FilamentRelograde\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelogradeAlert extends Model
{
    protected $table = 'relograde_alerts';

    protected $fillable = [
        'connection_id',
        'type',
        'severity',
        'currency',
        'current_amount',
        'threshold',
        'message',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'current_amount' => 'decimal:4',
            'threshold' => 'decimal:4',
            'resolved_at' => 'datetime',
        ];
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(RelogradeConnection::class, 'connection_id');
    }
}

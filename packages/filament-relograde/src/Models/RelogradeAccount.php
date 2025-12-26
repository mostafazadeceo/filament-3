<?php

namespace Haida\FilamentRelograde\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelogradeAccount extends Model
{
    protected $table = 'relograde_accounts';

    protected $fillable = [
        'connection_id',
        'currency',
        'state',
        'total_amount',
        'raw_json',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:4',
            'raw_json' => 'array',
            'synced_at' => 'datetime',
        ];
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(RelogradeConnection::class, 'connection_id');
    }
}

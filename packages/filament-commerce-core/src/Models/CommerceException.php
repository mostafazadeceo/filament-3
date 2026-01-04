<?php

namespace Haida\FilamentCommerceCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class CommerceException extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'type',
        'severity',
        'status',
        'title',
        'description',
        'entity_type',
        'entity_id',
        'created_by_user_id',
        'assigned_to_user_id',
        'resolved_by_user_id',
        'resolved_at',
        'resolution_note',
        'metadata',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function getTable(): string
    {
        return config('filament-commerce-core.tables.exceptions', 'commerce_exceptions');
    }
}

<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Models;

use Haida\FilamentMailOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailAlias extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'domain_id',
        'source',
        'destinations',
        'is_wildcard',
        'status',
        'sync_status',
        'last_error',
        'mailu_synced_at',
        'comment',
    ];

    protected $casts = [
        'destinations' => 'array',
        'is_wildcard' => 'bool',
        'mailu_synced_at' => 'datetime',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(MailDomain::class, 'domain_id');
    }

    public function getTable(): string
    {
        return config('filament-mailops.tables.aliases', 'mailops_aliases');
    }
}

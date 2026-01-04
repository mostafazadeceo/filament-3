<?php

namespace Haida\PageBuilder\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageTemplateRevision extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'template_id',
        'version',
        'status',
        'payload',
        'published_at',
        'created_by_user_id',
        'notes',
    ];

    protected $casts = [
        'payload' => 'array',
        'published_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(PageTemplate::class, 'template_id');
    }

    public function getTable(): string
    {
        return config('page-builder.tables.revisions', 'page_builder_revisions');
    }
}

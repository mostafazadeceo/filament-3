<?php

namespace Haida\ContentCms\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsPageRevision extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'page_id',
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

    public function page(): BelongsTo
    {
        return $this->belongsTo(CmsPage::class, 'page_id');
    }

    public function getTable(): string
    {
        return config('content-cms.tables.page_revisions', 'content_cms_page_revisions');
    }
}

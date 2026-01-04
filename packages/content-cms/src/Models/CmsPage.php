<?php

namespace Haida\ContentCms\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsPage extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'site_id',
        'slug',
        'title',
        'status',
        'seo',
        'draft_content',
        'published_content',
        'published_at',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'seo' => 'array',
        'draft_content' => 'array',
        'published_content' => 'array',
        'published_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(CmsPageRevision::class, 'page_id');
    }

    public function getTable(): string
    {
        return config('content-cms.tables.pages', 'content_cms_pages');
    }
}

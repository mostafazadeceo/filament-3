<?php

namespace Haida\Blog\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogTag extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'site_id',
        'name',
        'slug',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogPost::class,
            config('blog.tables.post_tag', 'blog_post_tag'),
            'tag_id',
            'post_id'
        );
    }

    public function getTable(): string
    {
        return config('blog.tables.tags', 'blog_tags');
    }
}

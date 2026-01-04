<?php

namespace Haida\Blog\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'site_id',
        'name',
        'slug',
        'description',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'category_id');
    }

    public function getTable(): string
    {
        return config('blog.tables.categories', 'blog_categories');
    }
}

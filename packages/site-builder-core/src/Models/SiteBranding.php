<?php

namespace Haida\SiteBuilderCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteBranding extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'site_id',
        'brand_name',
        'logo_path',
        'favicon_path',
        'primary_color',
        'secondary_color',
        'font_family',
        'footer_text',
        'powered_by_enabled',
    ];

    protected $casts = [
        'powered_by_enabled' => 'bool',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function getTable(): string
    {
        return config('site-builder-core.tables.site_brandings', 'site_brandings');
    }
}

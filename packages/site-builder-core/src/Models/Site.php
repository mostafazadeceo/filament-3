<?php

namespace Haida\SiteBuilderCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Haida\SiteBuilderCore\Enums\SiteStatus;
use Haida\SiteBuilderCore\Enums\SiteType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Site extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'type',
        'status',
        'default_locale',
        'currency',
        'timezone',
        'theme_key',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function branding(): HasOne
    {
        return $this->hasOne(SiteBranding::class, 'site_id');
    }

    public function publishHistories(): HasMany
    {
        return $this->hasMany(SitePublishHistory::class, 'site_id');
    }

    public function statusLabel(): string
    {
        return SiteStatus::tryFrom((string) $this->status)?->label() ?? (string) $this->status;
    }

    public function typeLabel(): string
    {
        return SiteType::tryFrom((string) $this->type)?->label() ?? (string) $this->type;
    }

    public function getTable(): string
    {
        return config('site-builder-core.tables.sites', 'sites');
    }

    protected static function booted(): void
    {
        static::created(function (Site $site): void {
            if ($site->branding()->exists()) {
                return;
            }

            $defaults = config('site-builder-core.defaults', []);

            $site->branding()->create([
                'tenant_id' => $site->tenant_id,
                'brand_name' => $site->name,
                'primary_color' => $defaults['primary_color'] ?? null,
                'secondary_color' => $defaults['secondary_color'] ?? null,
                'font_family' => $defaults['font_family'] ?? null,
                'powered_by_enabled' => (bool) ($defaults['powered_by_enabled'] ?? true),
            ]);
        });
    }
}

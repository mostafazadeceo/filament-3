<?php

namespace Haida\TenancyDomains\Models;

use Filamat\IamSuite\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteDomain extends Model
{
    public const SERVICE_ALL = 'all';

    public const SERVICE_SITE = 'site';

    public const SERVICE_BLOG = 'blog';

    public const SERVICE_STOREFRONT = 'storefront';

    public const SERVICE_CHAT = 'chat';

    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_FAILED = 'failed';

    public const TLS_STATUS_NOT_REQUESTED = 'not_requested';

    public const TLS_STATUS_PENDING = 'pending';

    public const TLS_STATUS_ISSUED = 'issued';

    public const TLS_STATUS_FAILED = 'failed';

    protected $guarded = [];

    protected $casts = [
        'verified_at' => 'datetime',
        'last_checked_at' => 'datetime',
        'is_primary' => 'bool',
        'tls_requested_at' => 'datetime',
        'tls_last_attempted_at' => 'datetime',
        'tls_issued_at' => 'datetime',
        'tls_expires_at' => 'datetime',
    ];

    /**
     * @return array<string, string>
     */
    public static function serviceOptions(): array
    {
        return [
            self::SERVICE_ALL => 'همه سرویس‌ها',
            self::SERVICE_SITE => 'سایت',
            self::SERVICE_BLOG => 'وبلاگ',
            self::SERVICE_STOREFRONT => 'فروشگاه',
            self::SERVICE_CHAT => 'چت',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function services(): array
    {
        return array_keys(self::serviceOptions());
    }

    public function getTable(): string
    {
        return config('tenancy-domains.tables.site_domains', 'site_domains');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}

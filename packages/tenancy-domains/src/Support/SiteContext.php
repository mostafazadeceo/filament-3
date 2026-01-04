<?php

namespace Haida\TenancyDomains\Support;

class SiteContext
{
    private static ?int $tenantId = null;
    private static ?int $siteId = null;
    private static ?string $host = null;

    public static function set(?int $tenantId, ?int $siteId, ?string $host): void
    {
        self::$tenantId = $tenantId;
        self::$siteId = $siteId;
        self::$host = $host;
    }

    public static function getTenantId(): ?int
    {
        return self::$tenantId;
    }

    public static function getSiteId(): ?int
    {
        return self::$siteId;
    }

    public static function getHost(): ?string
    {
        return self::$host;
    }

    public static function clear(): void
    {
        self::$tenantId = null;
        self::$siteId = null;
        self::$host = null;
    }
}

<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Support;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\ImpersonationService;
use Filament\Facades\Filament;

class TenantContext
{
    private static ?Tenant $tenant = null;

    private static bool $bypass = false;

    public static function setTenant(?Tenant $tenant): void
    {
        self::$tenant = $tenant;
    }

    public static function getTenant(): ?Tenant
    {
        if (self::$bypass) {
            return null;
        }

        if (self::$tenant) {
            return self::$tenant;
        }

        if (function_exists('session') && session()->has(ImpersonationService::SESSION_IMPERSONATED_TENANT)) {
            $tenantId = session()->get(ImpersonationService::SESSION_IMPERSONATED_TENANT);
            if ($tenantId) {
                return Tenant::query()->find($tenantId);
            }
        }

        if (class_exists(Filament::class) && Filament::getTenant()) {
            return Filament::getTenant();
        }

        return null;
    }

    public static function getTenantId(): ?int
    {
        return self::getTenant()?->getKey();
    }

    public static function bypass(bool $value = true): void
    {
        self::$bypass = $value;
    }

    public static function shouldBypass(): bool
    {
        if (self::$bypass) {
            return true;
        }

        if (function_exists('session') && session()->has(ImpersonationService::SESSION_IMPERSONATOR)) {
            return false;
        }

        if (! class_exists(Filament::class)) {
            return false;
        }

        $panelId = Filament::getCurrentPanel()?->getId();
        $superPanels = (array) config('filamat-iam.super_admin_panels', ['admin']);

        return $panelId !== null && in_array($panelId, $superPanels, true);
    }
}

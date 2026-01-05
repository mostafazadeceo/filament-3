<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Support;

use Filamat\IamSuite\Models\Organization;
use Filamat\IamSuite\Models\Tenant;
use Illuminate\Contracts\Auth\Authenticatable;

final class OrganizationAccess
{
    public static function isOrganizationOwner(Organization $organization, ?Authenticatable $user = null): bool
    {
        $user ??= auth()->user();
        if (! $user) {
            return false;
        }

        $ownerId = $organization->owner_user_id;
        if (! $ownerId) {
            return false;
        }

        return (string) $ownerId === (string) $user->getAuthIdentifier();
    }

    public static function currentOrganization(?Tenant $tenant = null): ?Organization
    {
        $tenant ??= TenantContext::getTenant();
        if (! $tenant) {
            return null;
        }

        return $tenant->organization;
    }

    public static function isCurrentOrganizationOwner(?Authenticatable $user = null): bool
    {
        $organization = self::currentOrganization();
        if (! $organization) {
            return false;
        }

        return self::isOrganizationOwner($organization, $user);
    }
}

<?php

namespace Haida\FilamentLoyaltyClub\Policies;

use Haida\FilamentLoyaltyClub\Models\LoyaltyBadge;
use Haida\FilamentLoyaltyClub\Policies\Concerns\HandlesLoyaltyPermissions;

class LoyaltyBadgePolicy
{
    use HandlesLoyaltyPermissions;

    public function viewAny(): bool
    {
        return $this->allow('loyalty.badge.view');
    }

    public function view(LoyaltyBadge $record): bool
    {
        return $this->allow('loyalty.badge.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('loyalty.badge.manage');
    }

    public function update(LoyaltyBadge $record): bool
    {
        return $this->allow('loyalty.badge.manage', $record);
    }

    public function delete(LoyaltyBadge $record): bool
    {
        return $this->allow('loyalty.badge.manage', $record);
    }

    public function restore(LoyaltyBadge $record): bool
    {
        return $this->allow('loyalty.badge.manage', $record);
    }

    public function forceDelete(LoyaltyBadge $record): bool
    {
        return $this->allow('loyalty.badge.manage', $record);
    }
}

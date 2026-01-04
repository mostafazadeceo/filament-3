<?php

namespace Haida\FilamentLoyaltyClub\Policies;

use Haida\FilamentLoyaltyClub\Models\LoyaltyTier;
use Haida\FilamentLoyaltyClub\Policies\Concerns\HandlesLoyaltyPermissions;

class LoyaltyTierPolicy
{
    use HandlesLoyaltyPermissions;

    public function viewAny(): bool
    {
        return $this->allow('loyalty.tier.view');
    }

    public function view(LoyaltyTier $record): bool
    {
        return $this->allow('loyalty.tier.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('loyalty.tier.manage');
    }

    public function update(LoyaltyTier $record): bool
    {
        return $this->allow('loyalty.tier.manage', $record);
    }

    public function delete(LoyaltyTier $record): bool
    {
        return $this->allow('loyalty.tier.manage', $record);
    }

    public function restore(LoyaltyTier $record): bool
    {
        return $this->allow('loyalty.tier.manage', $record);
    }

    public function forceDelete(LoyaltyTier $record): bool
    {
        return $this->allow('loyalty.tier.manage', $record);
    }
}

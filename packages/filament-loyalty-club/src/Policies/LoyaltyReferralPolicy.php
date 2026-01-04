<?php

namespace Haida\FilamentLoyaltyClub\Policies;

use Haida\FilamentLoyaltyClub\Models\LoyaltyReferral;
use Haida\FilamentLoyaltyClub\Policies\Concerns\HandlesLoyaltyPermissions;

class LoyaltyReferralPolicy
{
    use HandlesLoyaltyPermissions;

    public function viewAny(): bool
    {
        return $this->allow('loyalty.referral.view');
    }

    public function view(LoyaltyReferral $record): bool
    {
        return $this->allow('loyalty.referral.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('loyalty.referral.manage');
    }

    public function update(LoyaltyReferral $record): bool
    {
        return $this->allow('loyalty.referral.manage', $record);
    }

    public function delete(LoyaltyReferral $record): bool
    {
        return $this->allow('loyalty.referral.manage', $record);
    }

    public function restore(LoyaltyReferral $record): bool
    {
        return $this->allow('loyalty.referral.manage', $record);
    }

    public function forceDelete(LoyaltyReferral $record): bool
    {
        return $this->allow('loyalty.referral.manage', $record);
    }
}

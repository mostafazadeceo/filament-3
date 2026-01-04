<?php

namespace Haida\FilamentLoyaltyClub\Policies;

use Haida\FilamentLoyaltyClub\Models\LoyaltyReferralProgram;
use Haida\FilamentLoyaltyClub\Policies\Concerns\HandlesLoyaltyPermissions;

class LoyaltyReferralProgramPolicy
{
    use HandlesLoyaltyPermissions;

    public function viewAny(): bool
    {
        return $this->allow('loyalty.referral.program.view');
    }

    public function view(LoyaltyReferralProgram $record): bool
    {
        return $this->allow('loyalty.referral.program.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('loyalty.referral.program.manage');
    }

    public function update(LoyaltyReferralProgram $record): bool
    {
        return $this->allow('loyalty.referral.program.manage', $record);
    }

    public function delete(LoyaltyReferralProgram $record): bool
    {
        return $this->allow('loyalty.referral.program.manage', $record);
    }

    public function restore(LoyaltyReferralProgram $record): bool
    {
        return $this->allow('loyalty.referral.program.manage', $record);
    }

    public function forceDelete(LoyaltyReferralProgram $record): bool
    {
        return $this->allow('loyalty.referral.program.manage', $record);
    }
}

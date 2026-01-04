<?php

namespace Haida\FilamentLoyaltyClub\Policies;

use Haida\FilamentLoyaltyClub\Models\LoyaltyReward;
use Haida\FilamentLoyaltyClub\Policies\Concerns\HandlesLoyaltyPermissions;

class LoyaltyRewardPolicy
{
    use HandlesLoyaltyPermissions;

    public function viewAny(): bool
    {
        return $this->allow('loyalty.reward.view');
    }

    public function view(LoyaltyReward $record): bool
    {
        return $this->allow('loyalty.reward.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('loyalty.reward.manage');
    }

    public function update(LoyaltyReward $record): bool
    {
        return $this->allow('loyalty.reward.manage', $record);
    }

    public function delete(LoyaltyReward $record): bool
    {
        return $this->allow('loyalty.reward.manage', $record);
    }

    public function restore(LoyaltyReward $record): bool
    {
        return $this->allow('loyalty.reward.manage', $record);
    }

    public function forceDelete(LoyaltyReward $record): bool
    {
        return $this->allow('loyalty.reward.manage', $record);
    }

    public function redeem(LoyaltyReward $record): bool
    {
        return $this->allow('loyalty.reward.redeem', $record);
    }
}

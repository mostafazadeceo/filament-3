<?php

namespace Haida\FilamentLoyaltyClub\Policies;

use Haida\FilamentLoyaltyClub\Models\LoyaltyPointsRule;
use Haida\FilamentLoyaltyClub\Policies\Concerns\HandlesLoyaltyPermissions;

class LoyaltyPointsRulePolicy
{
    use HandlesLoyaltyPermissions;

    public function viewAny(): bool
    {
        return $this->allow('loyalty.rule.view');
    }

    public function view(LoyaltyPointsRule $record): bool
    {
        return $this->allow('loyalty.rule.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('loyalty.rule.manage');
    }

    public function update(LoyaltyPointsRule $record): bool
    {
        return $this->allow('loyalty.rule.manage', $record);
    }

    public function delete(LoyaltyPointsRule $record): bool
    {
        return $this->allow('loyalty.rule.manage', $record);
    }

    public function restore(LoyaltyPointsRule $record): bool
    {
        return $this->allow('loyalty.rule.manage', $record);
    }

    public function forceDelete(LoyaltyPointsRule $record): bool
    {
        return $this->allow('loyalty.rule.manage', $record);
    }
}

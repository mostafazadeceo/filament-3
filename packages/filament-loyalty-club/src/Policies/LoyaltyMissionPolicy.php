<?php

namespace Haida\FilamentLoyaltyClub\Policies;

use Haida\FilamentLoyaltyClub\Models\LoyaltyMission;
use Haida\FilamentLoyaltyClub\Policies\Concerns\HandlesLoyaltyPermissions;

class LoyaltyMissionPolicy
{
    use HandlesLoyaltyPermissions;

    public function viewAny(): bool
    {
        return $this->allow('loyalty.mission.view');
    }

    public function view(LoyaltyMission $record): bool
    {
        return $this->allow('loyalty.mission.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('loyalty.mission.manage');
    }

    public function update(LoyaltyMission $record): bool
    {
        return $this->allow('loyalty.mission.manage', $record);
    }

    public function delete(LoyaltyMission $record): bool
    {
        return $this->allow('loyalty.mission.manage', $record);
    }

    public function restore(LoyaltyMission $record): bool
    {
        return $this->allow('loyalty.mission.manage', $record);
    }

    public function forceDelete(LoyaltyMission $record): bool
    {
        return $this->allow('loyalty.mission.manage', $record);
    }
}

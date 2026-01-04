<?php

namespace Haida\FilamentLoyaltyClub\Policies;

use Haida\FilamentLoyaltyClub\Models\LoyaltyCampaign;
use Haida\FilamentLoyaltyClub\Policies\Concerns\HandlesLoyaltyPermissions;

class LoyaltyCampaignPolicy
{
    use HandlesLoyaltyPermissions;

    public function viewAny(): bool
    {
        return $this->allow('loyalty.campaign.view');
    }

    public function view(LoyaltyCampaign $record): bool
    {
        return $this->allow('loyalty.campaign.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('loyalty.campaign.manage');
    }

    public function update(LoyaltyCampaign $record): bool
    {
        return $this->allow('loyalty.campaign.manage', $record);
    }

    public function delete(LoyaltyCampaign $record): bool
    {
        return $this->allow('loyalty.campaign.manage', $record);
    }

    public function restore(LoyaltyCampaign $record): bool
    {
        return $this->allow('loyalty.campaign.manage', $record);
    }

    public function forceDelete(LoyaltyCampaign $record): bool
    {
        return $this->allow('loyalty.campaign.manage', $record);
    }

    public function send(LoyaltyCampaign $record): bool
    {
        return $this->allow('loyalty.campaign.send', $record);
    }
}

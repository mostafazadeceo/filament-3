<?php

namespace Haida\FilamentLoyaltyClub\Policies;

use Haida\FilamentLoyaltyClub\Models\LoyaltyEvent;
use Haida\FilamentLoyaltyClub\Policies\Concerns\HandlesLoyaltyPermissions;

class LoyaltyEventPolicy
{
    use HandlesLoyaltyPermissions;

    public function viewAny(): bool
    {
        return $this->allow('loyalty.audit.view');
    }

    public function view(LoyaltyEvent $record): bool
    {
        return $this->allow('loyalty.audit.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('loyalty.event.ingest');
    }

    public function update(LoyaltyEvent $record): bool
    {
        return false;
    }

    public function delete(LoyaltyEvent $record): bool
    {
        return false;
    }
}

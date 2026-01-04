<?php

namespace Haida\FilamentLoyaltyClub\Policies;

use Haida\FilamentLoyaltyClub\Models\LoyaltyFraudSignal;
use Haida\FilamentLoyaltyClub\Policies\Concerns\HandlesLoyaltyPermissions;

class LoyaltyFraudSignalPolicy
{
    use HandlesLoyaltyPermissions;

    public function viewAny(): bool
    {
        return $this->allow('loyalty.fraud.view');
    }

    public function view(LoyaltyFraudSignal $record): bool
    {
        return $this->allow('loyalty.fraud.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('loyalty.fraud.manage');
    }

    public function update(LoyaltyFraudSignal $record): bool
    {
        return $this->allow('loyalty.fraud.manage', $record);
    }

    public function delete(LoyaltyFraudSignal $record): bool
    {
        return $this->allow('loyalty.fraud.manage', $record);
    }

    public function restore(LoyaltyFraudSignal $record): bool
    {
        return $this->allow('loyalty.fraud.manage', $record);
    }

    public function forceDelete(LoyaltyFraudSignal $record): bool
    {
        return $this->allow('loyalty.fraud.manage', $record);
    }
}

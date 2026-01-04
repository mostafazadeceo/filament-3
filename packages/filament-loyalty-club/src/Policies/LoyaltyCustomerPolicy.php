<?php

namespace Haida\FilamentLoyaltyClub\Policies;

use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Policies\Concerns\HandlesLoyaltyPermissions;

class LoyaltyCustomerPolicy
{
    use HandlesLoyaltyPermissions;

    public function viewAny(): bool
    {
        return $this->allow('loyalty.customer.view');
    }

    public function view(LoyaltyCustomer $record): bool
    {
        return $this->allow('loyalty.customer.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('loyalty.customer.manage');
    }

    public function update(LoyaltyCustomer $record): bool
    {
        return $this->allow('loyalty.customer.manage', $record);
    }

    public function delete(LoyaltyCustomer $record): bool
    {
        return $this->allow('loyalty.customer.manage', $record);
    }

    public function restore(LoyaltyCustomer $record): bool
    {
        return $this->allow('loyalty.customer.manage', $record);
    }

    public function forceDelete(LoyaltyCustomer $record): bool
    {
        return $this->allow('loyalty.customer.manage', $record);
    }
}

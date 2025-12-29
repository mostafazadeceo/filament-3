<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\Party;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class PartyPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.party.view');
    }

    public function view(Party $party): bool
    {
        return $this->allow('accounting.party.view', $party);
    }

    public function create(): bool
    {
        return $this->allow('accounting.party.manage');
    }

    public function update(Party $party): bool
    {
        return $this->allow('accounting.party.manage', $party);
    }

    public function delete(Party $party): bool
    {
        return $this->allow('accounting.party.manage', $party);
    }
}

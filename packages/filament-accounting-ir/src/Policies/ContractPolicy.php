<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\Contract;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class ContractPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.contract.view');
    }

    public function view(Contract $contract): bool
    {
        return $this->allow('accounting.contract.view', $contract);
    }

    public function create(): bool
    {
        return $this->allow('accounting.contract.manage');
    }

    public function update(Contract $contract): bool
    {
        return $this->allow('accounting.contract.manage', $contract);
    }

    public function delete(Contract $contract): bool
    {
        return $this->allow('accounting.contract.manage', $contract);
    }
}

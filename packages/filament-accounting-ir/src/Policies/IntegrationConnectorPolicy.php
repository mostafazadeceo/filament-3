<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\IntegrationConnector;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class IntegrationConnectorPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.integration.view');
    }

    public function view(IntegrationConnector $record): bool
    {
        return $this->allow('accounting.integration.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.integration.manage');
    }

    public function update(IntegrationConnector $record): bool
    {
        return $this->allow('accounting.integration.manage', $record);
    }

    public function delete(IntegrationConnector $record): bool
    {
        return $this->allow('accounting.integration.manage', $record);
    }

    public function restore(IntegrationConnector $record): bool
    {
        return $this->allow('accounting.integration.manage', $record);
    }

    public function forceDelete(IntegrationConnector $record): bool
    {
        return $this->allow('accounting.integration.manage', $record);
    }
}

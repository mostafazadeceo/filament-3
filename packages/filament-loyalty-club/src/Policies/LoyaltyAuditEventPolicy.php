<?php

namespace Haida\FilamentLoyaltyClub\Policies;

use Haida\FilamentLoyaltyClub\Models\LoyaltyAuditEvent;
use Haida\FilamentLoyaltyClub\Policies\Concerns\HandlesLoyaltyPermissions;

class LoyaltyAuditEventPolicy
{
    use HandlesLoyaltyPermissions;

    public function viewAny(): bool
    {
        return $this->allow('loyalty.audit.view');
    }

    public function view(LoyaltyAuditEvent $record): bool
    {
        return $this->allow('loyalty.audit.view', $record);
    }

    public function create(): bool
    {
        return false;
    }

    public function update(LoyaltyAuditEvent $record): bool
    {
        return false;
    }

    public function delete(LoyaltyAuditEvent $record): bool
    {
        return false;
    }
}

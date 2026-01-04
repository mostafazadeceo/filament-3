<?php

namespace Haida\FilamentMarketplaceConnectors\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentMarketplaceConnectors\Models\MarketplaceSyncJob;

class MarketplaceSyncJobPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny(['marketplace.connectors.manage', 'marketplace.connectors.sync'], null, $user);
    }

    public function view(User $user, MarketplaceSyncJob $record): bool
    {
        return IamAuthorization::allowsAny(['marketplace.connectors.manage', 'marketplace.connectors.sync'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('marketplace.connectors.sync', null, $user);
    }

    public function update(User $user, MarketplaceSyncJob $record): bool
    {
        return IamAuthorization::allows('marketplace.connectors.sync', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, MarketplaceSyncJob $record): bool
    {
        return IamAuthorization::allows('marketplace.connectors.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}

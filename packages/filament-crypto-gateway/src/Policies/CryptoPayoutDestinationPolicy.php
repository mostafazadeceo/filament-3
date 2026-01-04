<?php

namespace Haida\FilamentCryptoGateway\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoGateway\Models\CryptoPayoutDestination;

class CryptoPayoutDestinationPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.payout_destinations.view',
            'crypto.payout_destinations.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoPayoutDestination $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.payout_destinations.view',
            'crypto.payout_destinations.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.payout_destinations.manage', null, $user);
    }

    public function update(User $user, CryptoPayoutDestination $record): bool
    {
        return IamAuthorization::allows('crypto.payout_destinations.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoPayoutDestination $record): bool
    {
        return IamAuthorization::allows('crypto.payout_destinations.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoPayoutDestination $record): bool
    {
        return IamAuthorization::allows('crypto.payout_destinations.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoPayoutDestination $record): bool
    {
        return IamAuthorization::allows('crypto.payout_destinations.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}

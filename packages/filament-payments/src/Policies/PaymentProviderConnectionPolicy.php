<?php

namespace Haida\FilamentPayments\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentPayments\Models\PaymentProviderConnection;

class PaymentProviderConnectionPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'payments.view',
            'payments.manage',
        ], null, $user);
    }

    public function view(User $user, PaymentProviderConnection $record): bool
    {
        return IamAuthorization::allowsAny([
            'payments.view',
            'payments.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('payments.manage', null, $user);
    }

    public function update(User $user, PaymentProviderConnection $record): bool
    {
        return IamAuthorization::allows('payments.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, PaymentProviderConnection $record): bool
    {
        return IamAuthorization::allows('payments.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, PaymentProviderConnection $record): bool
    {
        return IamAuthorization::allows('payments.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, PaymentProviderConnection $record): bool
    {
        return IamAuthorization::allows('payments.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}

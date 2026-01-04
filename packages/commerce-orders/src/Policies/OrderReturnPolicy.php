<?php

namespace Haida\CommerceOrders\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\CommerceOrders\Models\OrderReturn;

class OrderReturnPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.view',
            'commerce.order.manage',
            'commerce.order.return.view',
            'commerce.order.return.manage',
        ], null, $user);
    }

    public function view(User $user, OrderReturn $record): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.view',
            'commerce.order.manage',
            'commerce.order.return.view',
            'commerce.order.return.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.manage',
            'commerce.order.return.manage',
        ], null, $user);
    }

    public function update(User $user, OrderReturn $record): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.manage',
            'commerce.order.return.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, OrderReturn $record): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.manage',
            'commerce.order.return.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, OrderReturn $record): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.manage',
            'commerce.order.return.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, OrderReturn $record): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.manage',
            'commerce.order.return.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}

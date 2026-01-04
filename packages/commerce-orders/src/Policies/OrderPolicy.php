<?php

namespace Haida\CommerceOrders\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\CommerceOrders\Models\Order;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.view',
            'commerce.order.manage',
        ], null, $user);
    }

    public function view(User $user, Order $order): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.view',
            'commerce.order.manage',
        ], IamAuthorization::resolveTenantFromRecord($order), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('commerce.order.manage', null, $user);
    }

    public function update(User $user, Order $order): bool
    {
        return IamAuthorization::allows('commerce.order.manage', IamAuthorization::resolveTenantFromRecord($order), $user);
    }

    public function delete(User $user, Order $order): bool
    {
        return IamAuthorization::allows('commerce.order.manage', IamAuthorization::resolveTenantFromRecord($order), $user);
    }

    public function restore(User $user, Order $order): bool
    {
        return IamAuthorization::allows('commerce.order.manage', IamAuthorization::resolveTenantFromRecord($order), $user);
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return IamAuthorization::allows('commerce.order.manage', IamAuthorization::resolveTenantFromRecord($order), $user);
    }
}

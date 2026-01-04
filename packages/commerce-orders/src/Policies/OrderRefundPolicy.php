<?php

namespace Haida\CommerceOrders\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\CommerceOrders\Models\OrderRefund;

class OrderRefundPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.view',
            'commerce.order.manage',
            'commerce.order.refund.view',
            'commerce.order.refund.manage',
        ], null, $user);
    }

    public function view(User $user, OrderRefund $record): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.view',
            'commerce.order.manage',
            'commerce.order.refund.view',
            'commerce.order.refund.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.manage',
            'commerce.order.refund.manage',
        ], null, $user);
    }

    public function update(User $user, OrderRefund $record): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.manage',
            'commerce.order.refund.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, OrderRefund $record): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.manage',
            'commerce.order.refund.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, OrderRefund $record): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.manage',
            'commerce.order.refund.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, OrderRefund $record): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.order.manage',
            'commerce.order.refund.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}

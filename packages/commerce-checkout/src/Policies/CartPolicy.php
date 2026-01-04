<?php

namespace Haida\CommerceCheckout\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\CommerceCheckout\Models\Cart;

class CartPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.cart.view',
            'commerce.cart.manage',
        ], null, $user);
    }

    public function view(User $user, Cart $cart): bool
    {
        if ($cart->user_id && $cart->user_id === $user->getKey()) {
            return true;
        }

        return IamAuthorization::allowsAny([
            'commerce.cart.view',
            'commerce.cart.manage',
        ], IamAuthorization::resolveTenantFromRecord($cart), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('commerce.cart.manage', null, $user);
    }

    public function update(User $user, Cart $cart): bool
    {
        if ($cart->user_id && $cart->user_id === $user->getKey()) {
            return true;
        }

        return IamAuthorization::allows('commerce.cart.manage', IamAuthorization::resolveTenantFromRecord($cart), $user);
    }

    public function delete(User $user, Cart $cart): bool
    {
        return $this->update($user, $cart);
    }
}

<?php

namespace Haida\CommerceCheckout\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\CommerceCheckout\Models\CartItem;

class CartItemPolicy
{
    public function view(User $user, CartItem $item): bool
    {
        if ($item->cart && $item->cart->user_id && $item->cart->user_id === $user->getKey()) {
            return true;
        }

        return IamAuthorization::allowsAny([
            'commerce.cart.view',
            'commerce.cart.manage',
        ], IamAuthorization::resolveTenantFromRecord($item), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('commerce.cart.manage', null, $user);
    }

    public function update(User $user, CartItem $item): bool
    {
        if ($item->cart && $item->cart->user_id && $item->cart->user_id === $user->getKey()) {
            return true;
        }

        return IamAuthorization::allows('commerce.cart.manage', IamAuthorization::resolveTenantFromRecord($item), $user);
    }

    public function delete(User $user, CartItem $item): bool
    {
        return $this->update($user, $item);
    }
}

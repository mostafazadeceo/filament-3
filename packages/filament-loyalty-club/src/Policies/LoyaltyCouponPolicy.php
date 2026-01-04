<?php

namespace Haida\FilamentLoyaltyClub\Policies;

use Haida\FilamentLoyaltyClub\Models\LoyaltyCoupon;
use Haida\FilamentLoyaltyClub\Policies\Concerns\HandlesLoyaltyPermissions;

class LoyaltyCouponPolicy
{
    use HandlesLoyaltyPermissions;

    public function viewAny(): bool
    {
        return $this->allow('loyalty.coupon.view');
    }

    public function view(LoyaltyCoupon $record): bool
    {
        return $this->allow('loyalty.coupon.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('loyalty.coupon.manage');
    }

    public function update(LoyaltyCoupon $record): bool
    {
        return $this->allow('loyalty.coupon.manage', $record);
    }

    public function delete(LoyaltyCoupon $record): bool
    {
        return $this->allow('loyalty.coupon.manage', $record);
    }

    public function restore(LoyaltyCoupon $record): bool
    {
        return $this->allow('loyalty.coupon.manage', $record);
    }

    public function forceDelete(LoyaltyCoupon $record): bool
    {
        return $this->allow('loyalty.coupon.manage', $record);
    }

    public function issue(LoyaltyCoupon $record): bool
    {
        return $this->allow('loyalty.coupon.issue', $record);
    }

    public function redeem(LoyaltyCoupon $record): bool
    {
        return $this->allow('loyalty.coupon.redeem', $record);
    }
}

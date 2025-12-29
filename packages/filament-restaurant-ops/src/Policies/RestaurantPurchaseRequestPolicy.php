<?php

namespace Haida\FilamentRestaurantOps\Policies;

use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseRequest;
use Haida\FilamentRestaurantOps\Policies\Concerns\HandlesRestaurantPermissions;

class RestaurantPurchaseRequestPolicy
{
    use HandlesRestaurantPermissions;

    public function viewAny(): bool
    {
        return $this->allow('restaurant.purchase_request.view');
    }

    public function view(RestaurantPurchaseRequest $request): bool
    {
        return $this->allow('restaurant.purchase_request.view', $request);
    }

    public function create(): bool
    {
        return $this->allow('restaurant.purchase_request.manage');
    }

    public function update(RestaurantPurchaseRequest $request): bool
    {
        return $this->allow('restaurant.purchase_request.manage', $request);
    }

    public function delete(RestaurantPurchaseRequest $request): bool
    {
        return $this->allow('restaurant.purchase_request.manage', $request);
    }

    public function restore(RestaurantPurchaseRequest $request): bool
    {
        return $this->allow('restaurant.purchase_request.manage', $request);
    }

    public function forceDelete(RestaurantPurchaseRequest $request): bool
    {
        return $this->allow('restaurant.purchase_request.manage', $request);
    }

    public function approve(RestaurantPurchaseRequest $request): bool
    {
        return $this->allow('restaurant.purchase_request.approve', $request);
    }
}

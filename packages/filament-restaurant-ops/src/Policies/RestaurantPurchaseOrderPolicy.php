<?php

namespace Haida\FilamentRestaurantOps\Policies;

use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseOrder;
use Haida\FilamentRestaurantOps\Policies\Concerns\HandlesRestaurantPermissions;

class RestaurantPurchaseOrderPolicy
{
    use HandlesRestaurantPermissions;

    public function viewAny(): bool
    {
        return $this->allow('restaurant.purchase_order.view');
    }

    public function view(RestaurantPurchaseOrder $order): bool
    {
        return $this->allow('restaurant.purchase_order.view', $order);
    }

    public function create(): bool
    {
        return $this->allow('restaurant.purchase_order.manage');
    }

    public function update(RestaurantPurchaseOrder $order): bool
    {
        return $this->allow('restaurant.purchase_order.manage', $order);
    }

    public function delete(RestaurantPurchaseOrder $order): bool
    {
        return $this->allow('restaurant.purchase_order.manage', $order);
    }

    public function restore(RestaurantPurchaseOrder $order): bool
    {
        return $this->allow('restaurant.purchase_order.manage', $order);
    }

    public function forceDelete(RestaurantPurchaseOrder $order): bool
    {
        return $this->allow('restaurant.purchase_order.manage', $order);
    }

    public function approve(RestaurantPurchaseOrder $order): bool
    {
        return $this->allow('restaurant.purchase_order.approve', $order);
    }

    public function send(RestaurantPurchaseOrder $order): bool
    {
        return $this->allow('restaurant.purchase_order.send', $order);
    }
}

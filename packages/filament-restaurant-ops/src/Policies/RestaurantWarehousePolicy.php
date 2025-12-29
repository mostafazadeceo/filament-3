<?php

namespace Haida\FilamentRestaurantOps\Policies;

use Haida\FilamentRestaurantOps\Models\RestaurantWarehouse;
use Haida\FilamentRestaurantOps\Policies\Concerns\HandlesRestaurantPermissions;

class RestaurantWarehousePolicy
{
    use HandlesRestaurantPermissions;

    public function viewAny(): bool
    {
        return $this->allow('restaurant.warehouse.view');
    }

    public function view(RestaurantWarehouse $warehouse): bool
    {
        return $this->allow('restaurant.warehouse.view', $warehouse);
    }

    public function create(): bool
    {
        return $this->allow('restaurant.warehouse.manage');
    }

    public function update(RestaurantWarehouse $warehouse): bool
    {
        return $this->allow('restaurant.warehouse.manage', $warehouse);
    }

    public function delete(RestaurantWarehouse $warehouse): bool
    {
        return $this->allow('restaurant.warehouse.manage', $warehouse);
    }

    public function restore(RestaurantWarehouse $warehouse): bool
    {
        return $this->allow('restaurant.warehouse.manage', $warehouse);
    }

    public function forceDelete(RestaurantWarehouse $warehouse): bool
    {
        return $this->allow('restaurant.warehouse.manage', $warehouse);
    }
}

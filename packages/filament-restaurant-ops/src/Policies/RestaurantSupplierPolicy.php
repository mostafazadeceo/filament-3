<?php

namespace Haida\FilamentRestaurantOps\Policies;

use Haida\FilamentRestaurantOps\Models\RestaurantSupplier;
use Haida\FilamentRestaurantOps\Policies\Concerns\HandlesRestaurantPermissions;

class RestaurantSupplierPolicy
{
    use HandlesRestaurantPermissions;

    public function viewAny(): bool
    {
        return $this->allow('restaurant.supplier.view');
    }

    public function view(RestaurantSupplier $supplier): bool
    {
        return $this->allow('restaurant.supplier.view', $supplier);
    }

    public function create(): bool
    {
        return $this->allow('restaurant.supplier.manage');
    }

    public function update(RestaurantSupplier $supplier): bool
    {
        return $this->allow('restaurant.supplier.manage', $supplier);
    }

    public function delete(RestaurantSupplier $supplier): bool
    {
        return $this->allow('restaurant.supplier.manage', $supplier);
    }

    public function restore(RestaurantSupplier $supplier): bool
    {
        return $this->allow('restaurant.supplier.manage', $supplier);
    }

    public function forceDelete(RestaurantSupplier $supplier): bool
    {
        return $this->allow('restaurant.supplier.manage', $supplier);
    }
}

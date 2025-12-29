<?php

namespace Haida\FilamentRestaurantOps\Policies;

use Haida\FilamentRestaurantOps\Models\RestaurantUom;
use Haida\FilamentRestaurantOps\Policies\Concerns\HandlesRestaurantPermissions;

class RestaurantUomPolicy
{
    use HandlesRestaurantPermissions;

    public function viewAny(): bool
    {
        return $this->allow('restaurant.uom.view');
    }

    public function view(RestaurantUom $uom): bool
    {
        return $this->allow('restaurant.uom.view', $uom);
    }

    public function create(): bool
    {
        return $this->allow('restaurant.uom.manage');
    }

    public function update(RestaurantUom $uom): bool
    {
        return $this->allow('restaurant.uom.manage', $uom);
    }

    public function delete(RestaurantUom $uom): bool
    {
        return $this->allow('restaurant.uom.manage', $uom);
    }

    public function restore(RestaurantUom $uom): bool
    {
        return $this->allow('restaurant.uom.manage', $uom);
    }

    public function forceDelete(RestaurantUom $uom): bool
    {
        return $this->allow('restaurant.uom.manage', $uom);
    }
}

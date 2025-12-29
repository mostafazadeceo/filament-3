<?php

namespace Haida\FilamentRestaurantOps\Policies;

use Haida\FilamentRestaurantOps\Models\RestaurantMenuItem;
use Haida\FilamentRestaurantOps\Policies\Concerns\HandlesRestaurantPermissions;

class RestaurantMenuItemPolicy
{
    use HandlesRestaurantPermissions;

    public function viewAny(): bool
    {
        return $this->allow('restaurant.menu_item.view');
    }

    public function view(RestaurantMenuItem $item): bool
    {
        return $this->allow('restaurant.menu_item.view', $item);
    }

    public function create(): bool
    {
        return $this->allow('restaurant.menu_item.manage');
    }

    public function update(RestaurantMenuItem $item): bool
    {
        return $this->allow('restaurant.menu_item.manage', $item);
    }

    public function delete(RestaurantMenuItem $item): bool
    {
        return $this->allow('restaurant.menu_item.manage', $item);
    }

    public function restore(RestaurantMenuItem $item): bool
    {
        return $this->allow('restaurant.menu_item.manage', $item);
    }

    public function forceDelete(RestaurantMenuItem $item): bool
    {
        return $this->allow('restaurant.menu_item.manage', $item);
    }
}

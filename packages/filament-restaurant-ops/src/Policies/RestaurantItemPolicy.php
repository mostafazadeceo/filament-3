<?php

namespace Haida\FilamentRestaurantOps\Policies;

use Haida\FilamentRestaurantOps\Models\RestaurantItem;
use Haida\FilamentRestaurantOps\Policies\Concerns\HandlesRestaurantPermissions;

class RestaurantItemPolicy
{
    use HandlesRestaurantPermissions;

    public function viewAny(): bool
    {
        return $this->allow('restaurant.item.view');
    }

    public function view(RestaurantItem $item): bool
    {
        return $this->allow('restaurant.item.view', $item);
    }

    public function create(): bool
    {
        return $this->allow('restaurant.item.manage');
    }

    public function update(RestaurantItem $item): bool
    {
        return $this->allow('restaurant.item.manage', $item);
    }

    public function delete(RestaurantItem $item): bool
    {
        return $this->allow('restaurant.item.manage', $item);
    }

    public function restore(RestaurantItem $item): bool
    {
        return $this->allow('restaurant.item.manage', $item);
    }

    public function forceDelete(RestaurantItem $item): bool
    {
        return $this->allow('restaurant.item.manage', $item);
    }
}

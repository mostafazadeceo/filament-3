<?php

namespace Haida\FilamentRestaurantOps\Policies;

use Haida\FilamentRestaurantOps\Models\RestaurantInventoryDoc;
use Haida\FilamentRestaurantOps\Policies\Concerns\HandlesRestaurantPermissions;

class RestaurantInventoryDocPolicy
{
    use HandlesRestaurantPermissions;

    public function viewAny(): bool
    {
        return $this->allow('restaurant.inventory_doc.view');
    }

    public function view(RestaurantInventoryDoc $doc): bool
    {
        return $this->allow('restaurant.inventory_doc.view', $doc);
    }

    public function create(): bool
    {
        return $this->allow('restaurant.inventory_doc.manage');
    }

    public function update(RestaurantInventoryDoc $doc): bool
    {
        return $this->allow('restaurant.inventory_doc.manage', $doc);
    }

    public function delete(RestaurantInventoryDoc $doc): bool
    {
        return $this->allow('restaurant.inventory_doc.manage', $doc);
    }

    public function restore(RestaurantInventoryDoc $doc): bool
    {
        return $this->allow('restaurant.inventory_doc.manage', $doc);
    }

    public function forceDelete(RestaurantInventoryDoc $doc): bool
    {
        return $this->allow('restaurant.inventory_doc.manage', $doc);
    }

    public function post(RestaurantInventoryDoc $doc): bool
    {
        return $this->allow('restaurant.inventory_doc.post', $doc);
    }
}

<?php

namespace Haida\FilamentRestaurantOps\Policies;

use Haida\FilamentRestaurantOps\Models\RestaurantMenuSale;
use Haida\FilamentRestaurantOps\Policies\Concerns\HandlesRestaurantPermissions;

class RestaurantMenuSalePolicy
{
    use HandlesRestaurantPermissions;

    public function viewAny(): bool
    {
        return $this->allow('restaurant.menu_sale.view');
    }

    public function view(RestaurantMenuSale $sale): bool
    {
        return $this->allow('restaurant.menu_sale.view', $sale);
    }

    public function create(): bool
    {
        return $this->allow('restaurant.menu_sale.manage');
    }

    public function update(RestaurantMenuSale $sale): bool
    {
        return $this->allow('restaurant.menu_sale.manage', $sale);
    }

    public function delete(RestaurantMenuSale $sale): bool
    {
        return $this->allow('restaurant.menu_sale.manage', $sale);
    }

    public function restore(RestaurantMenuSale $sale): bool
    {
        return $this->allow('restaurant.menu_sale.manage', $sale);
    }

    public function forceDelete(RestaurantMenuSale $sale): bool
    {
        return $this->allow('restaurant.menu_sale.manage', $sale);
    }

    public function post(RestaurantMenuSale $sale): bool
    {
        return $this->allow('restaurant.menu_sale.post', $sale);
    }
}

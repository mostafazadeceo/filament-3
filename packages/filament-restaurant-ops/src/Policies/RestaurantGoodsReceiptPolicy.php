<?php

namespace Haida\FilamentRestaurantOps\Policies;

use Haida\FilamentRestaurantOps\Models\RestaurantGoodsReceipt;
use Haida\FilamentRestaurantOps\Policies\Concerns\HandlesRestaurantPermissions;

class RestaurantGoodsReceiptPolicy
{
    use HandlesRestaurantPermissions;

    public function viewAny(): bool
    {
        return $this->allow('restaurant.goods_receipt.view');
    }

    public function view(RestaurantGoodsReceipt $receipt): bool
    {
        return $this->allow('restaurant.goods_receipt.view', $receipt);
    }

    public function create(): bool
    {
        return $this->allow('restaurant.goods_receipt.manage');
    }

    public function update(RestaurantGoodsReceipt $receipt): bool
    {
        return $this->allow('restaurant.goods_receipt.manage', $receipt);
    }

    public function delete(RestaurantGoodsReceipt $receipt): bool
    {
        return $this->allow('restaurant.goods_receipt.manage', $receipt);
    }

    public function restore(RestaurantGoodsReceipt $receipt): bool
    {
        return $this->allow('restaurant.goods_receipt.manage', $receipt);
    }

    public function forceDelete(RestaurantGoodsReceipt $receipt): bool
    {
        return $this->allow('restaurant.goods_receipt.manage', $receipt);
    }

    public function post(RestaurantGoodsReceipt $receipt): bool
    {
        return $this->allow('restaurant.goods_receipt.post', $receipt);
    }
}

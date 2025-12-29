<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\ProductService;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class ProductServicePolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.product.view');
    }

    public function view(ProductService $product): bool
    {
        return $this->allow('accounting.product.view', $product);
    }

    public function create(): bool
    {
        return $this->allow('accounting.product.manage');
    }

    public function update(ProductService $product): bool
    {
        return $this->allow('accounting.product.manage', $product);
    }

    public function delete(ProductService $product): bool
    {
        return $this->allow('accounting.product.manage', $product);
    }
}

<?php

namespace Haida\FilamentLoyaltyClub\Adapters;

use Haida\FilamentLoyaltyClub\Contracts\PurchaseAdapterInterface;
use Haida\FilamentLoyaltyClub\Support\PurchaseData;

class CompositePurchaseAdapter implements PurchaseAdapterInterface
{
    public function __construct(
        protected ?CommerceOrdersAdapter $ordersAdapter = null,
        protected ?AccountingSalesInvoiceAdapter $invoiceAdapter = null,
        protected ?RestaurantMenuSaleAdapter $menuSaleAdapter = null,
        protected ?FallbackPurchaseAdapter $fallbackAdapter = null,
    ) {}

    public function resolve(array $payload): PurchaseData
    {
        if ($this->hasOrderPayload($payload)) {
            return $this->getOrdersAdapter()->resolve($payload);
        }

        if ($this->hasInvoicePayload($payload)) {
            return $this->getInvoiceAdapter()->resolve($payload);
        }

        if ($this->hasMenuSalePayload($payload)) {
            return $this->getMenuSaleAdapter()->resolve($payload);
        }

        return $this->getFallbackAdapter()->resolve($payload);
    }

    protected function hasOrderPayload(array $payload): bool
    {
        return isset($payload['order_id']) || isset($payload['orderId']);
    }

    protected function hasInvoicePayload(array $payload): bool
    {
        return isset($payload['sales_invoice_id']) || isset($payload['invoice_id']);
    }

    protected function hasMenuSalePayload(array $payload): bool
    {
        return isset($payload['menu_sale_id']) || isset($payload['sale_id']);
    }

    protected function getOrdersAdapter(): CommerceOrdersAdapter
    {
        return $this->ordersAdapter ?? new CommerceOrdersAdapter;
    }

    protected function getInvoiceAdapter(): AccountingSalesInvoiceAdapter
    {
        return $this->invoiceAdapter ?? new AccountingSalesInvoiceAdapter;
    }

    protected function getMenuSaleAdapter(): RestaurantMenuSaleAdapter
    {
        return $this->menuSaleAdapter ?? new RestaurantMenuSaleAdapter;
    }

    protected function getFallbackAdapter(): FallbackPurchaseAdapter
    {
        return $this->fallbackAdapter ?? new FallbackPurchaseAdapter;
    }
}

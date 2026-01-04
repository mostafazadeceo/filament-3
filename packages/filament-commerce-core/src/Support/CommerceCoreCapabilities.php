<?php

namespace Haida\FilamentCommerceCore\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentCommerceCore\Policies\CommerceBrandPolicy;
use Haida\FilamentCommerceCore\Policies\CommerceCategoryPolicy;
use Haida\FilamentCommerceCore\Policies\CommerceComplianceDigestPolicy;
use Haida\FilamentCommerceCore\Policies\CommerceCustomerPolicy;
use Haida\FilamentCommerceCore\Policies\CommerceExceptionPolicy;
use Haida\FilamentCommerceCore\Policies\CommerceFraudRulePolicy;
use Haida\FilamentCommerceCore\Policies\CommerceInventoryItemPolicy;
use Haida\FilamentCommerceCore\Policies\CommercePriceListPolicy;
use Haida\FilamentCommerceCore\Policies\CommercePricePolicy;
use Haida\FilamentCommerceCore\Policies\CommerceProductPolicy;
use Haida\FilamentCommerceCore\Policies\CommerceStockMovePolicy;
use Haida\FilamentCommerceCore\Policies\CommerceVariantPolicy;

final class CommerceCoreCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-commerce-core',
            self::permissions(),
            [
                'commerce_core' => true,
            ],
            [],
            [
                CommerceProductPolicy::class,
                CommerceCategoryPolicy::class,
                CommerceBrandPolicy::class,
                CommerceVariantPolicy::class,
                CommercePriceListPolicy::class,
                CommercePricePolicy::class,
                CommerceInventoryItemPolicy::class,
                CommerceStockMovePolicy::class,
                CommerceCustomerPolicy::class,
                CommerceExceptionPolicy::class,
                CommerceFraudRulePolicy::class,
                CommerceComplianceDigestPolicy::class,
            ],
            [
                'commerce_core' => 'تجارت',
                'commerce_core_catalog' => 'کاتالوگ',
                'commerce_core_pricing' => 'قیمت گذاری',
                'commerce_core_inventory' => 'موجودی',
                'commerce_core_customers' => 'مشتریان',
                'commerce_core_compliance' => 'انطباق و ضدتقلب',
            ]
        );

        self::$registered = true;
    }

    /**
     * @return array<int, string>
     */
    public static function permissions(): array
    {
        return [
            'commerce.catalog.view',
            'commerce.catalog.manage',
            'commerce.pricing.view',
            'commerce.pricing.manage',
            'commerce.inventory.view',
            'commerce.inventory.manage',
            'commerce.inventory.adjust',
            'commerce.customers.view',
            'commerce.customers.manage',
            'commerce.compliance.view',
            'commerce.compliance.manage',
            'commerce.compliance.resolve',
        ];
    }
}

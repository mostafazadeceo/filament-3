<?php

return [
    'tables' => [
        'products' => 'commerce_products',
        'variants' => 'commerce_variants',
        'categories' => 'commerce_categories',
        'brands' => 'commerce_brands',
        'category_product' => 'commerce_category_product',
        'price_lists' => 'commerce_price_lists',
        'prices' => 'commerce_prices',
        'inventory_items' => 'commerce_inventory_items',
        'customers' => 'commerce_customers',
        'orders' => 'commerce_orders',
        'returns' => 'commerce_returns',
        'stock_moves' => 'commerce_stock_moves',
        'idempotency_keys' => 'commerce_idempotency_keys',
        'audit_events' => 'commerce_audit_events',
        'exceptions' => 'commerce_exceptions',
        'fraud_rules' => 'commerce_fraud_rules',
        'compliance_digests' => 'commerce_compliance_digests',
    ],
    'defaults' => [
        'currency' => 'IRR',
    ],
    'api' => [
        'rate_limit' => '60,1',
    ],
    'compliance' => [
        'default_rules' => [
            [
                'key' => 'refund.amount_high',
                'name' => 'High refund amount',
                'thresholds' => [
                    'event' => 'refund',
                    'amount_gte' => 5000000,
                    'severity' => 'high',
                    'title' => 'High refund amount',
                    'description' => 'Refund amount exceeded configured threshold.',
                ],
            ],
            [
                'key' => 'discount.override_high',
                'name' => 'High manual discount',
                'thresholds' => [
                    'event' => 'discount_override',
                    'discount_rate_gte' => 0.3,
                    'severity' => 'medium',
                    'title' => 'Manual discount above threshold',
                ],
            ],
        ],
        'notifications' => [
            'panel' => 'tenant',
            'exception_event' => 'commerce.compliance.exception.created',
            'digest_event' => 'commerce.compliance.digest',
            'min_open' => 1,
        ],
    ],
];

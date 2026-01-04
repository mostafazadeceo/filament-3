<?php

return [
    'tables' => [
        'products' => 'commerce_catalog_products',
        'variants' => 'commerce_catalog_variants',
        'media' => 'commerce_catalog_media',
        'collections' => 'commerce_catalog_collections',
        'collection_product' => 'commerce_catalog_collection_product',
    ],
    'defaults' => [
        'currency' => 'IRR',
    ],
    'api' => [
        'rate_limit' => '60,1',
    ],
];

<?php

return [
    'tables' => [
        'connectors' => 'mkt_connectors',
        'accounts' => 'mkt_accounts',
        'tokens' => 'mkt_tokens',
        'sync_jobs' => 'mkt_sync_jobs',
        'sync_logs' => 'mkt_sync_logs',
        'rate_limits' => 'mkt_rate_limits',
    ],
    'api' => [
        'rate_limit' => '60,1',
    ],
    'providers' => [
        'amazon' => [
            'class' => Haida\FilamentMarketplaceConnectors\Connectors\AmazonSpApiConnector::class,
        ],
        'ebay' => [
            'class' => Haida\FilamentMarketplaceConnectors\Connectors\EbaySellConnector::class,
        ],
    ],
];

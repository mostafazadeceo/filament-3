<?php

return [
    'table_prefix' => 'accounting_ir_',

    'api' => [
        'enabled' => true,
        'rate_limit' => '60,1',
    ],

    'ledger' => [
        'base_currency' => 'IRR',
        'allow_negative_inventory' => false,
        'posting_requires_approval' => true,
        'posting_accounts' => [
            'sales_revenue' => null,
            'sales_tax' => null,
            'accounts_receivable' => null,
            'purchase_expense' => null,
            'purchase_tax' => null,
            'accounts_payable' => null,
            'cash' => null,
            'bank' => null,
        ],
    ],

    'tax' => [
        'vat_default_rate' => 0,
        'withholding_default_rate' => 0,
    ],

    'e_invoice' => [
        'enabled' => true,
        'default_driver' => 'mock',
        'default_payload_version' => 'v1',
        'retry_times' => 3,
        'material_types' => [
            'taxpayer_id',
            'private_key',
            'certificate',
            'api_token',
        ],
        'providers' => [
            'mock' => Vendor\FilamentAccountingIr\Services\EInvoice\Transports\MockEInvoiceTransport::class,
        ],
        'mappers' => [
            'v1' => Vendor\FilamentAccountingIr\Services\EInvoice\Mappers\V1Mapper::class,
        ],
    ],

    'integration' => [
        'connectors' => [
            'rest' => Vendor\FilamentAccountingIr\Services\Integration\Connectors\RestConnector::class,
            'csv' => Vendor\FilamentAccountingIr\Services\Integration\Connectors\CsvConnector::class,
        ],
    ],
];

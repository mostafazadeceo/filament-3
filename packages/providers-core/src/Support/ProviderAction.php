<?php

declare(strict_types=1);

namespace Haida\ProvidersCore\Support;

enum ProviderAction: string
{
    case SyncProducts = 'sync_products';
    case SyncInventory = 'sync_inventory';
    case CreateOrder = 'create_order';
    case FulfillOrder = 'fulfill_order';
    case FetchOrderStatus = 'fetch_order_status';
    case HandleWebhook = 'handle_webhook';
}

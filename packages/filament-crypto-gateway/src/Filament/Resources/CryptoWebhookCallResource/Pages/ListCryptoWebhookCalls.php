<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Filament\Resources\CryptoWebhookCallResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoWebhookCallResource;

class ListCryptoWebhookCalls extends ListRecords
{
    protected static string $resource = CryptoWebhookCallResource::class;
}

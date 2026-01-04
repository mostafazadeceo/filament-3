<?php

namespace Haida\FilamentMarketplaceConnectors\Filament\Resources\MarketplaceConnectorResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentMarketplaceConnectors\Filament\Resources\MarketplaceConnectorResource;

class ListMarketplaceConnectors extends ListRecordsWithCreate
{
    protected static string $resource = MarketplaceConnectorResource::class;
}

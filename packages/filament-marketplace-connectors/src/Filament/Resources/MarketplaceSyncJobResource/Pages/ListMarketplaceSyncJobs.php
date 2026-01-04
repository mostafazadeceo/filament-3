<?php

namespace Haida\FilamentMarketplaceConnectors\Filament\Resources\MarketplaceSyncJobResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentMarketplaceConnectors\Filament\Resources\MarketplaceSyncJobResource;

class ListMarketplaceSyncJobs extends ListRecordsWithCreate
{
    protected static string $resource = MarketplaceSyncJobResource::class;
}

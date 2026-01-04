<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Filament\Resources\CryptoPayoutDestinationResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoPayoutDestinationResource;

class ListCryptoPayoutDestinations extends ListRecords
{
    protected static string $resource = CryptoPayoutDestinationResource::class;
}

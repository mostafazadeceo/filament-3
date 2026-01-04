<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Filament\Resources\CryptoPayoutResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoPayoutResource;

class ListCryptoPayouts extends ListRecords
{
    protected static string $resource = CryptoPayoutResource::class;
}

<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Filament\Resources\CryptoProviderAccountResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoProviderAccountResource;

class ListCryptoProviderAccounts extends ListRecords
{
    protected static string $resource = CryptoProviderAccountResource::class;
}

<?php

namespace Haida\FilamentCurrencyRates\Resources\CurrencyRateResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentCurrencyRates\Resources\CurrencyRateResource;

class ListCurrencyRates extends ListRecordsWithCreate
{
    protected static string $resource = CurrencyRateResource::class;
}

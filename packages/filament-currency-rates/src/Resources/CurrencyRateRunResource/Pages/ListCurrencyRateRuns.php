<?php

namespace Haida\FilamentCurrencyRates\Resources\CurrencyRateRunResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentCurrencyRates\Resources\CurrencyRateRunResource;

class ListCurrencyRateRuns extends ListRecordsWithCreate
{
    protected static string $resource = CurrencyRateRunResource::class;
}

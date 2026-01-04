<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\WithholdingRateResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentAccountingIr\Filament\Resources\WithholdingRateResource;

class ListWithholdingRates extends ListRecordsWithCreate
{
    protected static string $resource = WithholdingRateResource::class;
}

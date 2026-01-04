<?php

namespace Haida\FilamentPayments\Filament\Resources\PaymentProviderConnectionResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentPayments\Filament\Resources\PaymentProviderConnectionResource;

class ListPaymentProviderConnections extends ListRecordsWithCreate
{
    protected static string $resource = PaymentProviderConnectionResource::class;
}

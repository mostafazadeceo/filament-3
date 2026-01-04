<?php

namespace Haida\FilamentPayments\Filament\Resources\PaymentIntentResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentPayments\Filament\Resources\PaymentIntentResource;

class ListPaymentIntents extends ListRecordsWithCreate
{
    protected static string $resource = PaymentIntentResource::class;
}

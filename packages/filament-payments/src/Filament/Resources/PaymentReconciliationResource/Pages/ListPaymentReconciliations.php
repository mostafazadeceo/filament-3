<?php

namespace Haida\FilamentPayments\Filament\Resources\PaymentReconciliationResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentPayments\Filament\Resources\PaymentReconciliationResource;

class ListPaymentReconciliations extends ListRecordsWithCreate
{
    protected static string $resource = PaymentReconciliationResource::class;
}

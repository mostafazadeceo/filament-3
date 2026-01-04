<?php

namespace Haida\FilamentPayments\Filament\Resources\PaymentRefundResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentPayments\Filament\Resources\PaymentRefundResource;

class ListPaymentRefunds extends ListRecordsWithCreate
{
    protected static string $resource = PaymentRefundResource::class;
}

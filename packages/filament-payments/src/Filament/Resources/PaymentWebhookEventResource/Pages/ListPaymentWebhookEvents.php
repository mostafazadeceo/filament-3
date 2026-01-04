<?php

namespace Haida\FilamentPayments\Filament\Resources\PaymentWebhookEventResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Haida\FilamentPayments\Filament\Resources\PaymentWebhookEventResource;

class ListPaymentWebhookEvents extends ListRecords
{
    protected static string $resource = PaymentWebhookEventResource::class;
}

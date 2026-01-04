<?php

namespace Haida\FilamentPayments\Filament\Resources\PaymentIntentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentPayments\Filament\Resources\PaymentIntentResource;

class CreatePaymentIntent extends CreateRecord
{
    protected static string $resource = PaymentIntentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();

        return $data;
    }
}

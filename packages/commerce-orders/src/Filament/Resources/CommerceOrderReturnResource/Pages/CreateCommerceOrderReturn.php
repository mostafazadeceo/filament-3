<?php

namespace Haida\CommerceOrders\Filament\Resources\CommerceOrderReturnResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderReturnResource;

class CreateCommerceOrderReturn extends CreateRecord
{
    protected static string $resource = CommerceOrderReturnResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['requested_by_user_id'] = auth()->id();
        $data['requested_at'] = $data['requested_at'] ?? now();

        return $data;
    }
}

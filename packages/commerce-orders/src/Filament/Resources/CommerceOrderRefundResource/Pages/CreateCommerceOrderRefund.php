<?php

namespace Haida\CommerceOrders\Filament\Resources\CommerceOrderRefundResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderRefundResource;

class CreateCommerceOrderRefund extends CreateRecord
{
    protected static string $resource = CommerceOrderRefundResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();

        return $data;
    }
}

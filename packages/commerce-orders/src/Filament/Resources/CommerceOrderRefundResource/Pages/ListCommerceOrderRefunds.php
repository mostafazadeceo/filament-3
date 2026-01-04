<?php

namespace Haida\CommerceOrders\Filament\Resources\CommerceOrderRefundResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderRefundResource;

class ListCommerceOrderRefunds extends ListRecordsWithCreate
{
    protected static string $resource = CommerceOrderRefundResource::class;
}

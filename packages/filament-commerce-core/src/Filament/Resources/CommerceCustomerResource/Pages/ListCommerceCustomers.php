<?php

namespace Haida\FilamentCommerceCore\Filament\Resources\CommerceCustomerResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceCustomerResource;

class ListCommerceCustomers extends ListRecordsWithCreate
{
    protected static string $resource = CommerceCustomerResource::class;
}

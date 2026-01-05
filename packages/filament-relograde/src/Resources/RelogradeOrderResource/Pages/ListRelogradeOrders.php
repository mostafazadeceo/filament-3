<?php

namespace Haida\FilamentRelograde\Resources\RelogradeOrderResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filament\Actions\CreateAction;
use Haida\FilamentRelograde\Resources\RelogradeOrderResource;
use Haida\FilamentRelograde\Support\RelogradeAuthorization;

class ListRelogradeOrders extends ListRecordsWithCreate
{
    protected static string $resource = RelogradeOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('ایجاد سفارش')
                ->visible(fn () => RelogradeAuthorization::can('orders_create')),
        ];
    }
}

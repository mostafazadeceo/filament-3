<?php

namespace Haida\FilamentRelograde\Resources\RelogradeOrderResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Haida\FilamentRelograde\Resources\RelogradeOrderResource;
use Haida\FilamentRelograde\Support\RelogradeAuthorization;

class ListRelogradeOrders extends ListRecords
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

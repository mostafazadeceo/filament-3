<?php

namespace Haida\FilamentRelograde\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Haida\FilamentRelograde\Models\RelogradeProduct;
use Haida\FilamentRelograde\Widgets\Concerns\RelogradeWidgetVisibility;

class RelogradeStockWidget extends StatsOverviewWidget
{
    use RelogradeWidgetVisibility;

    protected ?string $heading = 'موجودی محصولات';

    protected function getStats(): array
    {
        $stocked = RelogradeProduct::query()->where('is_stocked', true)->count();
        $outOfStock = RelogradeProduct::query()->where('is_stocked', false)->count();

        return [
            Stat::make('موجود', (string) $stocked),
            Stat::make('ناموجود', (string) $outOfStock),
        ];
    }
}

<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasEagerLoads
{
    public static function getEloquentQuery(): Builder
    {
        $eagerLoad = property_exists(static::class, 'eagerLoad') ? static::$eagerLoad : [];

        return parent::getEloquentQuery()->with($eagerLoad);
    }
}

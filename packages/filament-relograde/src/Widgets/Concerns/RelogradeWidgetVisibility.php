<?php

namespace Haida\FilamentRelograde\Widgets\Concerns;

trait RelogradeWidgetVisibility
{
    public static function canView(): bool
    {
        return request()->routeIs('filament.*.pages.relograde-dashboard');
    }
}

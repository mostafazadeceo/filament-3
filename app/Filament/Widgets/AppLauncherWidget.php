<?php

namespace App\Filament\Widgets;

use App\Support\Navigation\AppContext;
use App\Support\Navigation\AppNavigationCatalog;
use Filament\Widgets\Widget;

/**
 * Odoo-style app launcher for the Hub dashboard.
 *
 * Notes:
 * - Keep this widget "data-light": a curated list of entrypoints is faster and
 *   more predictable than trying to infer apps from Filament navigation.
 * - Each item is visibility-checked via Resource/Page accessors when possible.
 */
class AppLauncherWidget extends Widget
{
    protected static ?int $sort = -100;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = ['default' => 'full'];

    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.app-launcher-widget';

    protected function getViewData(): array
    {
        $items = AppNavigationCatalog::collectItems();
        $apps = array_values(AppNavigationCatalog::appsFromItems($items));

        foreach ($apps as &$app) {
            $app['url'] = AppNavigationCatalog::appSwitchUrl($app['key']);
            $app['external'] = false;
        }

        return [
            'apps' => $apps,
            'activeKey' => AppContext::get(),
        ];
    }
}

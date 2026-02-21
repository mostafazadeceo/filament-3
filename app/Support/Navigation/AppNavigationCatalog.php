<?php

namespace App\Support\Navigation;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Filament\Pages\Dashboard;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use UnitEnum;

class AppNavigationCatalog
{
    /**
     * @return array<NavigationItem>
     */
    public static function collectItems(?Panel $panel = null): array
    {
        $panel ??= Filament::getCurrentPanel();

        if (! $panel) {
            return [];
        }

        $items = [];

        foreach ($panel->getPages() as $page) {
            if (method_exists($page, 'shouldRegisterNavigation') && ($page::shouldRegisterNavigation() === false)) {
                continue;
            }

            if (method_exists($page, 'canAccess') && ($page::canAccess() === false)) {
                continue;
            }

            try {
                $items = array_merge($items, $page::getNavigationItems());
            } catch (RouteNotFoundException) {
                continue;
            } catch (\Throwable) {
                continue;
            }
        }

        foreach ($panel->getResources() as $resource) {
            if (method_exists($resource, 'shouldRegisterNavigation') && ($resource::shouldRegisterNavigation() === false)) {
                continue;
            }

            if (method_exists($resource, 'canAccess') && ($resource::canAccess() === false)) {
                continue;
            }

            if (method_exists($resource, 'hasPage') && ($resource::hasPage('index') === false)) {
                continue;
            }

            try {
                $items = array_merge($items, $resource::getNavigationItems());
            } catch (RouteNotFoundException) {
                continue;
            } catch (\Throwable) {
                continue;
            }
        }

        $items = array_values(array_filter($items, function ($item): bool {
            if (! $item instanceof NavigationItem) {
                return false;
            }

            if (! $item->isVisible()) {
                return false;
            }

            try {
                $item->getUrl();
            } catch (\Throwable) {
                return false;
            }

            return true;
        }));

        return $items;
    }

    /**
     * @return array<string, array{key: string, label: string, icon: string, description: string, count: int}>
     */
    public static function appsFromItems(array $items): array
    {
        $groupIcons = config('abrak_navigation.group_icons', []);
        $groupDescriptions = config('abrak_navigation.group_descriptions', []);

        $apps = [];

        foreach ($items as $item) {
            if (! $item instanceof NavigationItem) {
                continue;
            }

            $group = $item->getGroup();
            $label = self::normalizeGroupLabel($group);

            if ($label === null) {
                continue;
            }

            $key = AppContext::keyFromLabel($label);

            if (! isset($apps[$key])) {
                $apps[$key] = [
                    'key' => $key,
                    'label' => $label,
                    'icon' => Arr::get($groupIcons, $label, 'heroicon-o-squares-2x2'),
                    'description' => Arr::get($groupDescriptions, $label, ''),
                    'count' => 0,
                ];
            }

            $apps[$key]['count']++;
        }

        return $apps;
    }

    public static function normalizeGroupLabel(string|UnitEnum|null $group): ?string
    {
        if ($group instanceof UnitEnum) {
            return method_exists($group, 'getLabel') ? $group->getLabel() : $group->name;
        }

        if (is_string($group)) {
            $label = trim($group);
            return $label !== '' ? $label : null;
        }

        return null;
    }

    /**
     * @return array<NavigationItem>
     */
    public static function filterItemsByApp(array $items, ?string $appKey): array
    {
        if (! is_string($appKey) || $appKey === '') {
            return [];
        }

        return array_values(array_filter($items, function ($item) use ($appKey): bool {
            if (! $item instanceof NavigationItem) {
                return false;
            }

            $label = self::normalizeGroupLabel($item->getGroup());
            if ($label === null) {
                return false;
            }

            return AppContext::keyFromLabel($label) === $appKey;
        }));
    }

    public static function dashboardUrl(?Panel $panel = null): string
    {
        $panel ??= Filament::getCurrentPanel();

        if (! $panel) {
            return url('/');
        }

        foreach ($panel->getPages() as $page) {
            if (! is_string($page) || ! is_subclass_of($page, Dashboard::class)) {
                continue;
            }

            if (method_exists($page, 'canAccess') && ($page::canAccess() === false)) {
                continue;
            }

            try {
                return $page::getUrl(panel: $panel->getId());
            } catch (\Throwable) {
                continue;
            }
        }

        $params = [];
        if ($panel->hasTenancy()) {
            $params['tenant'] = Filament::getTenant();
        }

        // Last-resort fallback (should not happen in normal Filament setups).
        return $panel->route('home', $params);
    }

    public static function appSwitchUrl(string $key, ?Panel $panel = null): string
    {
        $panel ??= Filament::getCurrentPanel();

        if (! $panel) {
            return url('/');
        }

        $params = ['key' => $key];
        if ($panel->hasTenancy()) {
            $params['tenant'] = Filament::getTenant();
        }

        return $panel->route('app.switch', $params);
    }

    public static function homeUrl(?Panel $panel = null): string
    {
        // "Home" in Abrak UX means the App Launcher dashboard.
        return self::dashboardUrl($panel);
    }

    public static function firstUrlForApp(string $appKey, ?Panel $panel = null): ?string
    {
        $panel ??= Filament::getCurrentPanel();

        if (! $panel) {
            return null;
        }

        $items = self::filterItemsByApp(self::collectItems($panel), $appKey);

        if (! $items) {
            return null;
        }

        usort($items, fn (NavigationItem $a, NavigationItem $b): int => $a->getSort() <=> $b->getSort());

        foreach ($items as $item) {
            if (! $item->isVisible()) {
                continue;
            }

            try {
                $url = $item->getUrl();
            } catch (\Throwable) {
                continue;
            }

            if (is_string($url) && $url !== '') {
                return $url;
            }
        }

        return null;
    }
}

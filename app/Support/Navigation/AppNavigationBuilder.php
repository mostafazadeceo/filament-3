<?php

namespace App\Support\Navigation;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use UnitEnum;

class AppNavigationBuilder
{
    /**
     * @return array<NavigationGroup>
     */
    public static function build(?Panel $panel = null): array
    {
        $panel ??= Filament::getCurrentPanel();

        if (! $panel) {
            return [];
        }

        $items = AppNavigationCatalog::collectItems($panel);
        $apps = AppNavigationCatalog::appsFromItems($items);

        $current = AppContext::get();
        if (! is_string($current) || $current === '' || ! isset($apps[$current])) {
            $current = null;
            AppContext::set(null);
        }

        $filtered = $current ? AppNavigationCatalog::filterItemsByApp($items, $current) : [];
        $groups = self::groupItems($filtered, $panel->getNavigationGroups());

        $homeUrl = AppNavigationCatalog::homeUrl($panel);
        $homeItem = NavigationItem::make('اپلیکیشن‌ها')
            ->icon('heroicon-o-squares-2x2')
            ->url($homeUrl)
            ->sort(-999);

        $homeGroup = NavigationGroup::make('خانه')
            ->items([$homeItem]);

        return array_values(array_filter([
            $homeGroup,
            ...$groups,
        ]));
    }

    /**
     * @param  array<NavigationItem>  $items
     * @param  array<string | int, NavigationGroup | string>  $groupOrder
     * @return array<NavigationGroup>
     */
    private static function groupItems(array $items, array $groupOrder): array
    {
        $items = collect($items)
            ->filter(fn (NavigationItem $item): bool => $item->isVisible())
            ->sortBy(fn (NavigationItem $item): int => $item->getSort());

        $groups = $items->groupBy(function (NavigationItem $item): string {
            return serialize($item->getGroup());
        })->map(function (Collection $items, string $groupIndex) use ($groupOrder): NavigationGroup {
            $parentItems = $items->groupBy(fn (NavigationItem $item): string => $item->getParentItem() ?? '');

            $items = $parentItems->get('', collect())
                ->keyBy(fn (NavigationItem $item): string => $item->getLabel());

            $parentItems->except([''])->each(function (Collection $parentItemItems, string $parentItemLabel) use ($items): void {
                if (! $items->has($parentItemLabel)) {
                    return;
                }

                $items->get($parentItemLabel)->childItems($parentItemItems);
            });

            $items = $items->filter(fn (NavigationItem $item): bool => self::isNavigable($item));

            $groupName = unserialize($groupIndex);

            $itemsArray = $items->values()->all();

            if (blank($groupName)) {
                return NavigationGroup::make()->items($itemsArray);
            }

            $groupEnum = null;

            if ($groupName instanceof UnitEnum) {
                $groupEnum = $groupName;
                $groupName = $groupEnum->name;
            }

            $registeredGroup = collect($groupOrder)
                ->first(function (NavigationGroup | string $registeredGroup, string | int $registeredGroupIndex) use ($groupName) {
                    if ($registeredGroupIndex === $groupName) {
                        return true;
                    }

                    if ($registeredGroup === $groupName) {
                        return true;
                    }

                    if (! $registeredGroup instanceof NavigationGroup) {
                        return false;
                    }

                    return $registeredGroup->getLabel() === $groupName;
                });

            if ($registeredGroup instanceof NavigationGroup) {
                return $registeredGroup->items($itemsArray);
            }

            $group = NavigationGroup::make($registeredGroup ?? $groupName);

            if ($groupEnum && method_exists($groupEnum, 'getLabel')) {
                $group->label($groupEnum->getLabel());
            }

            if ($groupEnum && method_exists($groupEnum, 'getIcon')) {
                $group->icon($groupEnum->getIcon());
            }

            return $group->items($itemsArray);
        })->filter(fn (NavigationGroup $group): bool => filled($group->getItems()));

        return $groups
            ->sortBy(function (NavigationGroup $group, ?string $groupIndex) use ($groupOrder): int {
                if (blank($group->getLabel())) {
                    return -1;
                }

                $groupName = unserialize($groupIndex ?? '');
                $groupEnum = null;

                if ($groupName instanceof UnitEnum) {
                    $groupEnum = $groupName;
                    $groupName = $groupEnum->name;
                }

                $groupsToSearch = $groupOrder;

                if (Arr::first($groupOrder) instanceof NavigationGroup) {
                    $groupsToSearch = [
                        ...array_keys($groupOrder),
                        ...array_map(fn (NavigationGroup $registeredGroup): string => $registeredGroup->getLabel(), array_values($groupOrder)),
                    ];
                }

                $sort = array_search($groupName, $groupsToSearch, true);

                if ($groupEnum) {
                    $enumCaseSort = array_search($groupEnum, $groupEnum::cases(), true);
                    $sort = ($enumCaseSort !== false) ? $enumCaseSort : $sort;
                }

                if ($sort === false) {
                    return count($groupOrder);
                }

                return (int) $sort;
            })
            ->values()
            ->all();
    }

    private static function isNavigable(NavigationItem $item): bool
    {
        if (filled($item->getChildItems())) {
            return true;
        }

        try {
            return filled($item->getUrl());
        } catch (RouteNotFoundException) {
            return false;
        } catch (\Throwable) {
            return false;
        }
    }
}

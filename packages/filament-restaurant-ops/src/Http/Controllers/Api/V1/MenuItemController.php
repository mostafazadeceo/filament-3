<?php

namespace Haida\FilamentRestaurantOps\Http\Controllers\Api\V1;

use Haida\FilamentRestaurantOps\Http\Requests\StoreMenuItemRequest;
use Haida\FilamentRestaurantOps\Http\Requests\UpdateMenuItemRequest;
use Haida\FilamentRestaurantOps\Http\Resources\MenuItemResource;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuItem;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MenuItemController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(RestaurantMenuItem::class, 'menu_item');
    }

    public function index(): AnonymousResourceCollection
    {
        $items = RestaurantMenuItem::query()->latest()->paginate();

        return MenuItemResource::collection($items);
    }

    public function show(RestaurantMenuItem $menu_item): MenuItemResource
    {
        return new MenuItemResource($menu_item);
    }

    public function store(StoreMenuItemRequest $request): MenuItemResource
    {
        $item = RestaurantMenuItem::query()->create($request->validated());

        return new MenuItemResource($item);
    }

    public function update(UpdateMenuItemRequest $request, RestaurantMenuItem $menu_item): MenuItemResource
    {
        $menu_item->update($request->validated());

        return new MenuItemResource($menu_item->refresh());
    }

    public function destroy(RestaurantMenuItem $menu_item): array
    {
        $menu_item->delete();

        return ['status' => 'ok'];
    }
}

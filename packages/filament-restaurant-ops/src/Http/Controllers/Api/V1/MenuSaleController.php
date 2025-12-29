<?php

namespace Haida\FilamentRestaurantOps\Http\Controllers\Api\V1;

use Haida\FilamentRestaurantOps\Http\Requests\PostMenuSaleRequest;
use Haida\FilamentRestaurantOps\Http\Requests\StoreMenuSaleRequest;
use Haida\FilamentRestaurantOps\Http\Requests\UpdateMenuSaleRequest;
use Haida\FilamentRestaurantOps\Http\Resources\MenuSaleResource;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuSale;
use Haida\FilamentRestaurantOps\Services\RestaurantMenuSaleService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MenuSaleController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(RestaurantMenuSale::class, 'menu_sale');
    }

    public function index(): AnonymousResourceCollection
    {
        $sales = RestaurantMenuSale::query()
            ->with('lines')
            ->latest('sale_date')
            ->paginate();

        return MenuSaleResource::collection($sales);
    }

    public function show(RestaurantMenuSale $menu_sale): MenuSaleResource
    {
        $menu_sale->load('lines');

        return new MenuSaleResource($menu_sale);
    }

    public function store(StoreMenuSaleRequest $request): MenuSaleResource
    {
        $data = $request->validated();
        $lines = $data['lines'] ?? [];
        $status = $data['status'] ?? 'draft';
        unset($data['lines']);

        $sale = DB::transaction(function () use ($data, $lines): RestaurantMenuSale {
            $sale = RestaurantMenuSale::query()->create($data);
            $this->syncLines($sale, $lines);

            return $sale->refresh();
        });

        if ($status === 'posted') {
            $sale = app(RestaurantMenuSaleService::class)->post($sale);
        }

        return new MenuSaleResource($sale->load('lines'));
    }

    public function update(UpdateMenuSaleRequest $request, RestaurantMenuSale $menu_sale): MenuSaleResource
    {
        if ($menu_sale->status === 'posted') {
            throw ValidationException::withMessages([
                'status' => 'فروش قطعی قابل ویرایش نیست.',
            ]);
        }

        $data = $request->validated();
        $lines = $data['lines'] ?? null;
        $status = $data['status'] ?? $menu_sale->status;
        unset($data['lines']);

        $sale = DB::transaction(function () use ($menu_sale, $data, $lines): RestaurantMenuSale {
            $menu_sale->update($data);
            if (is_array($lines)) {
                $this->syncLines($menu_sale, $lines);
            }

            return $menu_sale->refresh();
        });

        if ($status === 'posted') {
            $sale = app(RestaurantMenuSaleService::class)->post($sale);
        }

        return new MenuSaleResource($sale->load('lines'));
    }

    public function post(PostMenuSaleRequest $request, RestaurantMenuSale $menu_sale): MenuSaleResource
    {
        $this->authorize('post', $menu_sale);

        $sale = app(RestaurantMenuSaleService::class)->post($menu_sale);

        return new MenuSaleResource($sale->load('lines'));
    }

    public function destroy(RestaurantMenuSale $menu_sale): array
    {
        if ($menu_sale->status === 'posted') {
            throw ValidationException::withMessages([
                'status' => 'فروش قطعی قابل حذف نیست.',
            ]);
        }

        $menu_sale->delete();

        return ['status' => 'ok'];
    }

    protected function syncLines(RestaurantMenuSale $sale, array $lines): void
    {
        $sale->lines()->delete();

        foreach ($lines as $line) {
            $sale->lines()->create([
                'menu_item_id' => $line['menu_item_id'] ?? null,
                'quantity' => $line['quantity'] ?? 0,
                'unit_price' => $line['unit_price'] ?? 0,
                'line_total' => $line['line_total'] ?? 0,
            ]);
        }
    }
}

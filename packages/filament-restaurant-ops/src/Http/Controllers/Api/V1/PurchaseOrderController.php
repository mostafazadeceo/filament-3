<?php

namespace Haida\FilamentRestaurantOps\Http\Controllers\Api\V1;

use Haida\FilamentRestaurantOps\Http\Requests\StorePurchaseOrder;
use Haida\FilamentRestaurantOps\Http\Requests\UpdatePurchaseOrder;
use Haida\FilamentRestaurantOps\Http\Resources\PurchaseOrderResource;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseOrder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseOrderController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(RestaurantPurchaseOrder::class, 'purchase_order');
    }

    public function index(): AnonymousResourceCollection
    {
        $orders = RestaurantPurchaseOrder::query()
            ->with('lines')
            ->latest('order_date')
            ->paginate();

        return PurchaseOrderResource::collection($orders);
    }

    public function show(RestaurantPurchaseOrder $purchase_order): PurchaseOrderResource
    {
        $purchase_order->load('lines');

        return new PurchaseOrderResource($purchase_order);
    }

    public function store(StorePurchaseOrder $request): PurchaseOrderResource
    {
        $data = $request->validated();
        $lines = $data['lines'] ?? [];
        unset($data['lines']);

        $order = DB::transaction(function () use ($data, $lines): RestaurantPurchaseOrder {
            $order = RestaurantPurchaseOrder::query()->create($data);
            $this->syncLines($order, $lines);

            return $order->refresh();
        });

        return new PurchaseOrderResource($order->load('lines'));
    }

    public function update(UpdatePurchaseOrder $request, RestaurantPurchaseOrder $purchase_order): PurchaseOrderResource
    {
        if ($purchase_order->status === 'received') {
            throw ValidationException::withMessages([
                'status' => 'سفارش دریافت‌شده قابل ویرایش نیست.',
            ]);
        }

        $data = $request->validated();
        $lines = $data['lines'] ?? null;
        unset($data['lines']);

        $order = DB::transaction(function () use ($purchase_order, $data, $lines): RestaurantPurchaseOrder {
            $purchase_order->update($data);
            if (is_array($lines)) {
                $this->syncLines($purchase_order, $lines);
            }

            return $purchase_order->refresh();
        });

        return new PurchaseOrderResource($order->load('lines'));
    }

    public function destroy(RestaurantPurchaseOrder $purchase_order): array
    {
        if ($purchase_order->status === 'received') {
            throw ValidationException::withMessages([
                'status' => 'سفارش دریافت‌شده قابل حذف نیست.',
            ]);
        }

        $purchase_order->delete();

        return ['status' => 'ok'];
    }

    protected function syncLines(RestaurantPurchaseOrder $order, array $lines): void
    {
        $order->lines()->delete();

        foreach ($lines as $line) {
            $order->lines()->create([
                'item_id' => $line['item_id'] ?? null,
                'uom_id' => $line['uom_id'] ?? null,
                'quantity' => $line['quantity'] ?? 0,
                'unit_price' => $line['unit_price'] ?? 0,
                'tax_rate' => $line['tax_rate'] ?? 0,
                'tax_amount' => $line['tax_amount'] ?? 0,
                'discount_amount' => $line['discount_amount'] ?? 0,
                'line_total' => $line['line_total'] ?? 0,
            ]);
        }
    }
}

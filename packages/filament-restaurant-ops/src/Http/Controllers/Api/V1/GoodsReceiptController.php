<?php

namespace Haida\FilamentRestaurantOps\Http\Controllers\Api\V1;

use Haida\FilamentRestaurantOps\Http\Requests\PostGoodsReceiptRequest;
use Haida\FilamentRestaurantOps\Http\Requests\StoreGoodsReceiptRequest;
use Haida\FilamentRestaurantOps\Http\Requests\UpdateGoodsReceiptRequest;
use Haida\FilamentRestaurantOps\Http\Resources\GoodsReceiptResource;
use Haida\FilamentRestaurantOps\Models\RestaurantGoodsReceipt;
use Haida\FilamentRestaurantOps\Services\RestaurantGoodsReceiptService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GoodsReceiptController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(RestaurantGoodsReceipt::class, 'goods_receipt');
    }

    public function index(): AnonymousResourceCollection
    {
        $receipts = RestaurantGoodsReceipt::query()
            ->with('lines')
            ->latest('receipt_date')
            ->paginate();

        return GoodsReceiptResource::collection($receipts);
    }

    public function show(RestaurantGoodsReceipt $goods_receipt): GoodsReceiptResource
    {
        $goods_receipt->load('lines');

        return new GoodsReceiptResource($goods_receipt);
    }

    public function store(StoreGoodsReceiptRequest $request): GoodsReceiptResource
    {
        $data = $request->validated();
        $lines = $data['lines'] ?? [];
        $status = $data['status'] ?? 'draft';
        unset($data['lines']);

        $receipt = DB::transaction(function () use ($data, $lines): RestaurantGoodsReceipt {
            $receipt = RestaurantGoodsReceipt::query()->create($data);
            $this->syncLines($receipt, $lines);

            return $receipt->refresh();
        });

        if ($status === 'posted') {
            $receipt = app(RestaurantGoodsReceiptService::class)->post($receipt);
        }

        return new GoodsReceiptResource($receipt->load('lines'));
    }

    public function update(UpdateGoodsReceiptRequest $request, RestaurantGoodsReceipt $goods_receipt): GoodsReceiptResource
    {
        if ($goods_receipt->status === 'posted') {
            throw ValidationException::withMessages([
                'status' => 'رسید قطعی قابل ویرایش نیست.',
            ]);
        }

        $data = $request->validated();
        $lines = $data['lines'] ?? null;
        $status = $data['status'] ?? $goods_receipt->status;
        unset($data['lines']);

        $receipt = DB::transaction(function () use ($goods_receipt, $data, $lines): RestaurantGoodsReceipt {
            $goods_receipt->update($data);
            if (is_array($lines)) {
                $this->syncLines($goods_receipt, $lines);
            }

            return $goods_receipt->refresh();
        });

        if ($status === 'posted') {
            $receipt = app(RestaurantGoodsReceiptService::class)->post($receipt);
        }

        return new GoodsReceiptResource($receipt->load('lines'));
    }

    public function post(PostGoodsReceiptRequest $request, RestaurantGoodsReceipt $goods_receipt): GoodsReceiptResource
    {
        $this->authorize('post', $goods_receipt);

        $receipt = app(RestaurantGoodsReceiptService::class)->post($goods_receipt);

        return new GoodsReceiptResource($receipt->load('lines'));
    }

    public function destroy(RestaurantGoodsReceipt $goods_receipt): array
    {
        if ($goods_receipt->status === 'posted') {
            throw ValidationException::withMessages([
                'status' => 'رسید قطعی قابل حذف نیست.',
            ]);
        }

        $goods_receipt->delete();

        return ['status' => 'ok'];
    }

    protected function syncLines(RestaurantGoodsReceipt $receipt, array $lines): void
    {
        $receipt->lines()->delete();

        foreach ($lines as $line) {
            $receipt->lines()->create([
                'item_id' => $line['item_id'] ?? null,
                'uom_id' => $line['uom_id'] ?? null,
                'quantity' => $line['quantity'] ?? 0,
                'unit_cost' => $line['unit_cost'] ?? 0,
                'tax_rate' => $line['tax_rate'] ?? 0,
                'tax_amount' => $line['tax_amount'] ?? 0,
                'batch_no' => $line['batch_no'] ?? null,
                'expires_at' => $line['expires_at'] ?? null,
                'line_total' => $line['line_total'] ?? 0,
            ]);
        }
    }
}

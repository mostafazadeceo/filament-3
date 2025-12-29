<?php

namespace Haida\FilamentRestaurantOps\Http\Controllers\Api\V1;

use Haida\FilamentRestaurantOps\Http\Requests\PostInventoryDocRequest;
use Haida\FilamentRestaurantOps\Http\Requests\StoreInventoryDocRequest;
use Haida\FilamentRestaurantOps\Http\Requests\UpdateInventoryDocRequest;
use Haida\FilamentRestaurantOps\Http\Resources\InventoryDocResource;
use Haida\FilamentRestaurantOps\Models\RestaurantInventoryDoc;
use Haida\FilamentRestaurantOps\Services\RestaurantInventoryDocService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryDocController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(RestaurantInventoryDoc::class, 'inventory_doc');
    }

    public function index(): AnonymousResourceCollection
    {
        $docs = RestaurantInventoryDoc::query()
            ->with('lines')
            ->latest('doc_date')
            ->paginate();

        return InventoryDocResource::collection($docs);
    }

    public function show(RestaurantInventoryDoc $inventory_doc): InventoryDocResource
    {
        $inventory_doc->load('lines');

        return new InventoryDocResource($inventory_doc);
    }

    public function store(StoreInventoryDocRequest $request): InventoryDocResource
    {
        $data = $request->validated();
        $lines = $data['lines'] ?? [];
        $status = $data['status'] ?? 'draft';
        unset($data['lines']);

        $doc = DB::transaction(function () use ($data, $lines): RestaurantInventoryDoc {
            $doc = RestaurantInventoryDoc::query()->create($data);
            $this->syncLines($doc, $lines);

            return $doc->refresh();
        });

        if ($status === 'posted') {
            $doc = app(RestaurantInventoryDocService::class)->post($doc);
        }

        return new InventoryDocResource($doc->load('lines'));
    }

    public function update(UpdateInventoryDocRequest $request, RestaurantInventoryDoc $inventory_doc): InventoryDocResource
    {
        if ($inventory_doc->status === 'posted') {
            throw ValidationException::withMessages([
                'status' => 'سند قطعی قابل ویرایش نیست.',
            ]);
        }

        $data = $request->validated();
        $lines = $data['lines'] ?? null;
        $status = $data['status'] ?? $inventory_doc->status;
        unset($data['lines']);

        $doc = DB::transaction(function () use ($inventory_doc, $data, $lines): RestaurantInventoryDoc {
            $inventory_doc->update($data);
            if (is_array($lines)) {
                $this->syncLines($inventory_doc, $lines);
            }

            return $inventory_doc->refresh();
        });

        if ($status === 'posted') {
            $doc = app(RestaurantInventoryDocService::class)->post($doc);
        }

        return new InventoryDocResource($doc->load('lines'));
    }

    public function post(PostInventoryDocRequest $request, RestaurantInventoryDoc $inventory_doc): InventoryDocResource
    {
        $this->authorize('post', $inventory_doc);

        $doc = app(RestaurantInventoryDocService::class)->post($inventory_doc);

        return new InventoryDocResource($doc->load('lines'));
    }

    public function destroy(RestaurantInventoryDoc $inventory_doc): array
    {
        if ($inventory_doc->status === 'posted') {
            throw ValidationException::withMessages([
                'status' => 'سند قطعی قابل حذف نیست.',
            ]);
        }

        $inventory_doc->delete();

        return ['status' => 'ok'];
    }

    protected function syncLines(RestaurantInventoryDoc $doc, array $lines): void
    {
        $doc->lines()->delete();

        foreach ($lines as $line) {
            $doc->lines()->create([
                'item_id' => $line['item_id'] ?? null,
                'uom_id' => $line['uom_id'] ?? null,
                'quantity' => $line['quantity'] ?? 0,
                'unit_cost' => $line['unit_cost'] ?? 0,
                'batch_no' => $line['batch_no'] ?? null,
                'expires_at' => $line['expires_at'] ?? null,
                'metadata' => $line['metadata'] ?? null,
            ]);
        }
    }
}

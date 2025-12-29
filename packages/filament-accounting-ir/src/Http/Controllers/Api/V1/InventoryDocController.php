<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\PostInventoryDocRequest;
use Vendor\FilamentAccountingIr\Http\Requests\StoreInventoryDocRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateInventoryDocRequest;
use Vendor\FilamentAccountingIr\Http\Resources\InventoryDocResource;
use Vendor\FilamentAccountingIr\Models\InventoryDoc;
use Vendor\FilamentAccountingIr\Services\InventoryDocService;

class InventoryDocController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = InventoryDoc::query()->with('lines')->latest('doc_date')->paginate();

        return InventoryDocResource::collection($items);
    }

    public function show(InventoryDoc $inventory_doc): InventoryDocResource
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

        $doc = DB::transaction(function () use ($data, $lines): InventoryDoc {
            $doc = InventoryDoc::query()->create($data);
            $this->syncLines($doc, $lines);

            return $doc->refresh();
        });

        if ($status === 'posted') {
            $doc = app(InventoryDocService::class)->post($doc);
        }

        return new InventoryDocResource($doc->load('lines'));
    }

    public function update(UpdateInventoryDocRequest $request, InventoryDoc $inventory_doc): InventoryDocResource
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

        $doc = DB::transaction(function () use ($inventory_doc, $data, $lines): InventoryDoc {
            $inventory_doc->update($data);
            if (is_array($lines)) {
                $this->syncLines($inventory_doc, $lines);
            }

            return $inventory_doc->refresh();
        });

        if ($status === 'posted') {
            $doc = app(InventoryDocService::class)->post($doc);
        }

        return new InventoryDocResource($doc->load('lines'));
    }

    public function post(PostInventoryDocRequest $request, InventoryDoc $inventory_doc): InventoryDocResource
    {
        $doc = app(InventoryDocService::class)->post($inventory_doc);

        return new InventoryDocResource($doc->load('lines'));
    }

    public function destroy(InventoryDoc $inventory_doc): array
    {
        if ($inventory_doc->status === 'posted') {
            throw ValidationException::withMessages([
                'status' => 'سند قطعی قابل حذف نیست.',
            ]);
        }

        $inventory_doc->delete();

        return ['status' => 'ok'];
    }

    protected function syncLines(InventoryDoc $doc, array $lines): void
    {
        $doc->lines()->delete();

        foreach ($lines as $line) {
            $doc->lines()->create([
                'inventory_item_id' => $line['inventory_item_id'] ?? null,
                'location_id' => $line['location_id'] ?? null,
                'quantity' => $line['quantity'] ?? 0,
                'unit_cost' => $line['unit_cost'] ?? null,
            ]);
        }
    }
}

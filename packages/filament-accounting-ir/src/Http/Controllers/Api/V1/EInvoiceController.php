<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreEInvoiceRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateEInvoiceRequest;
use Vendor\FilamentAccountingIr\Http\Resources\EInvoiceResource;
use Vendor\FilamentAccountingIr\Models\EInvoice;
use Vendor\FilamentAccountingIr\Services\EInvoice\EInvoiceService;

class EInvoiceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = EInvoice::query()->latest()->paginate();

        return EInvoiceResource::collection($items);
    }

    public function show(EInvoice $e_invoice): EInvoiceResource
    {
        $e_invoice->load(['lines', 'submissions', 'statusLogs']);

        return new EInvoiceResource($e_invoice);
    }

    public function store(StoreEInvoiceRequest $request): EInvoiceResource
    {
        $item = EInvoice::query()->create($request->validated());

        return new EInvoiceResource($item->load(['lines', 'submissions', 'statusLogs']));
    }

    public function update(UpdateEInvoiceRequest $request, EInvoice $e_invoice): EInvoiceResource
    {
        $e_invoice->update($request->validated());

        return new EInvoiceResource($e_invoice->load(['lines', 'submissions', 'statusLogs']));
    }

    public function destroy(EInvoice $e_invoice): array
    {
        $e_invoice->delete();

        return ['status' => 'ok'];
    }

    public function send(EInvoice $e_invoice): EInvoiceResource
    {
        app(EInvoiceService::class)->queue($e_invoice);

        return new EInvoiceResource($e_invoice->refresh()->load(['lines', 'submissions', 'statusLogs']));
    }
}

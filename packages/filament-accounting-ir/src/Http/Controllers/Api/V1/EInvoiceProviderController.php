<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreEInvoiceProviderRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateEInvoiceProviderRequest;
use Vendor\FilamentAccountingIr\Http\Resources\EInvoiceProviderResource;
use Vendor\FilamentAccountingIr\Models\EInvoiceProvider;

class EInvoiceProviderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = EInvoiceProvider::query()->latest()->paginate();

        return EInvoiceProviderResource::collection($items);
    }

    public function show(EInvoiceProvider $e_invoice_provider): EInvoiceProviderResource
    {
        return new EInvoiceProviderResource($e_invoice_provider);
    }

    public function store(StoreEInvoiceProviderRequest $request): EInvoiceProviderResource
    {
        $item = EInvoiceProvider::query()->create($request->validated());

        return new EInvoiceProviderResource($item);
    }

    public function update(UpdateEInvoiceProviderRequest $request, EInvoiceProvider $e_invoice_provider): EInvoiceProviderResource
    {
        $e_invoice_provider->update($request->validated());

        return new EInvoiceProviderResource($e_invoice_provider);
    }

    public function destroy(EInvoiceProvider $e_invoice_provider): array
    {
        $e_invoice_provider->delete();

        return ['status' => 'ok'];
    }
}

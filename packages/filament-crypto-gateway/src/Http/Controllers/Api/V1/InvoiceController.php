<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCryptoGateway\Http\Requests\StoreInvoiceRequest;
use Haida\FilamentCryptoGateway\Http\Resources\CryptoInvoiceResource;
use Haida\FilamentCryptoGateway\Models\CryptoInvoice;
use Haida\FilamentCryptoGateway\Services\InvoiceService;
use Illuminate\Http\JsonResponse;

class InvoiceController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(CryptoInvoice::class, 'invoice');
    }

    public function store(StoreInvoiceRequest $request, InvoiceService $service): CryptoInvoiceResource
    {
        $payload = $request->validated();
        $payload['tenant_id'] = (int) TenantContext::getTenantId();

        $invoice = $service->create($payload);

        return new CryptoInvoiceResource($invoice);
    }

    public function show(CryptoInvoice $invoice): CryptoInvoiceResource
    {
        return new CryptoInvoiceResource($invoice);
    }

    public function status(CryptoInvoice $invoice): JsonResponse
    {
        return response()->json([
            'id' => $invoice->getKey(),
            'status' => (string) $invoice->status,
            'is_final' => $invoice->is_final,
        ]);
    }

    public function refresh(CryptoInvoice $invoice, InvoiceService $service): CryptoInvoiceResource
    {
        $invoice = $service->refresh($invoice);

        return new CryptoInvoiceResource($invoice);
    }
}

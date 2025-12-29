<?php

namespace Vendor\FilamentAccountingIr\Services\EInvoice;

use Illuminate\Support\Facades\DB;
use Vendor\FilamentAccountingIr\Events\EInvoiceFailed;
use Vendor\FilamentAccountingIr\Events\EInvoiceSent;
use Vendor\FilamentAccountingIr\Models\EInvoice;
use Vendor\FilamentAccountingIr\Models\EInvoiceProvider;
use Vendor\FilamentAccountingIr\Models\EInvoiceStatusLog;
use Vendor\FilamentAccountingIr\Models\EInvoiceSubmission;
use Vendor\FilamentAccountingIr\Models\KeyMaterial;
use Vendor\FilamentAccountingIr\Services\EInvoice\DTOs\EInvoiceTransportResponse;

class EInvoiceEngine
{
    public function __construct(
        protected EInvoiceBuilder $builder,
        protected EInvoiceMapperRegistry $mapperRegistry,
        protected EInvoiceTransportRegistry $transportRegistry,
        protected KeyMaterialService $keyMaterialService,
    ) {}

    public function send(EInvoice $invoice): EInvoiceSubmission
    {
        $invoice->loadMissing(['company', 'provider', 'salesInvoice.party', 'salesInvoice.lines', 'lines']);

        $provider = $this->resolveProvider($invoice);
        $payloadVersion = $invoice->payload_version ?: (string) config('filament-accounting-ir.e_invoice.default_payload_version', 'v1');

        $domainInvoice = $this->builder->build($invoice);
        $mapper = $this->mapperRegistry->resolve($payloadVersion);
        $payload = $mapper->map($domainInvoice, $payloadVersion);

        $submission = DB::transaction(function () use ($invoice, $provider, $payload): EInvoiceSubmission {
            $submission = EInvoiceSubmission::query()->create([
                'e_invoice_id' => $invoice->getKey(),
                'provider_id' => $provider?->getKey(),
                'status' => 'pending',
                'request_payload' => $payload,
            ]);

            $invoice->update([
                'status' => 'queued',
                'payload_version' => $payload['version'] ?? $invoice->payload_version,
                'payload' => $payload,
            ]);

            EInvoiceStatusLog::query()->create([
                'e_invoice_id' => $invoice->getKey(),
                'status' => 'queued',
                'message' => 'queued for sending',
            ]);

            return $submission;
        });

        $response = $this->deliver($provider, $payload, $invoice->company_id);

        DB::transaction(function () use ($invoice, $submission, $response): void {
            $status = $response->status === 'sent' ? 'sent' : 'failed';

            $submission->update([
                'status' => $status,
                'correlation_id' => $response->correlationId,
                'response_payload' => $response->payload,
            ]);

            $invoice->update([
                'status' => $status,
                'unique_tax_id' => $response->uniqueTaxId,
                'issued_at' => $invoice->issued_at ?? now(),
            ]);

            EInvoiceStatusLog::query()->create([
                'e_invoice_id' => $invoice->getKey(),
                'status' => $status,
                'message' => $response->message,
                'metadata' => $response->payload,
            ]);
        });

        if ($response->status === 'sent') {
            event(new EInvoiceSent($invoice));
        } else {
            event(new EInvoiceFailed($invoice));
        }

        return $submission;
    }

    protected function resolveProvider(EInvoice $invoice): ?EInvoiceProvider
    {
        if ($invoice->provider) {
            return $invoice->provider;
        }

        $driver = (string) config('filament-accounting-ir.e_invoice.default_driver', 'mock');

        return EInvoiceProvider::query()
            ->where('company_id', $invoice->company_id)
            ->where('driver', $driver)
            ->where('is_active', true)
            ->first();
    }

    protected function deliver(?EInvoiceProvider $provider, array $payload, ?int $companyId): EInvoiceTransportResponse
    {
        if (! $provider) {
            return new EInvoiceTransportResponse(status: 'failed', message: 'provider not configured');
        }

        $transport = $this->transportRegistry->resolve($provider->driver);
        $keyMaterial = $this->resolveKeyMaterial($companyId);

        return $transport->send($provider, $payload, $keyMaterial);
    }

    protected function resolveKeyMaterial(?int $companyId): ?KeyMaterial
    {
        if (! $companyId) {
            return null;
        }

        return $this->keyMaterialService->getActive($companyId, 'private_key');
    }
}

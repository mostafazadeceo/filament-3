<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Webhooks;

use Filamat\IamSuite\Contracts\PaymentProviderInterface;
use Filamat\IamSuite\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentWebhookController
{
    public function __construct(
        protected PaymentProviderInterface $provider,
        protected WebhookService $webhookService
    ) {}

    public function __invoke(Request $request): Response
    {
        if ((bool) config('filamat-iam.webhooks.verify_inbound', true)) {
            $secret = (string) config('filamat-iam.webhooks.inbound_secrets.payment');
            if ($secret !== '') {
                $signature = (string) $request->header(config('filamat-iam.webhooks.signature_header'));
                $timestamp = (int) $request->header(config('filamat-iam.webhooks.timestamp_header'));
                $nonce = (string) $request->header(config('filamat-iam.webhooks.nonce_header'));

                if (! $signature || ! $timestamp || ! $nonce) {
                    return response(['message' => 'امضای وبهوک نامعتبر است.'], 401);
                }

                if (! $this->webhookService->verifySignature($secret, $request->all(), $signature, $timestamp, $nonce, 'payment')) {
                    return response(['message' => 'وبهوک رد شد.'], 401);
                }
            }
        }

        $this->provider->handleWebhook($request->all(), $request->headers->all());

        return response(['status' => 'ok'], 200);
    }
}

<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Contracts;

use Haida\FilamentCryptoGateway\DTOs\InvoiceCreateData;
use Haida\FilamentCryptoGateway\DTOs\PayoutCreateData;
use Haida\FilamentCryptoGateway\DTOs\ProviderInvoiceData;
use Haida\FilamentCryptoGateway\DTOs\ProviderPayoutData;
use Haida\FilamentCryptoGateway\DTOs\WebhookEventData;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;

interface ProviderAdapterInterface
{
    public function key(): string;

    /**
     * @return array<string, bool>
     */
    public function supports(): array;

    public function createInvoice(InvoiceCreateData $data, CryptoProviderAccount $account): ProviderInvoiceData;

    public function getInvoice(string $externalId, CryptoProviderAccount $account): ?ProviderInvoiceData;

    public function createPayout(PayoutCreateData $data, CryptoProviderAccount $account): ProviderPayoutData;

    public function getPayout(string $externalId, CryptoProviderAccount $account): ?ProviderPayoutData;

    /**
     * @param  array<string, mixed>  $headers
     */
    public function verifyAndParseWebhook(array $headers, string $rawPayload, string $ip, ?CryptoProviderAccount $account = null): WebhookEventData;
}

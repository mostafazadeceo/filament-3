<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\DTOs;

use Haida\FilamentCryptoGateway\Enums\CryptoInvoiceStatus;
use Haida\FilamentCryptoGateway\Enums\CryptoPayoutStatus;

final class WebhookEventData
{
    public function __construct(
        public readonly string $provider,
        public readonly string $eventId,
        public readonly string $eventType,
        public readonly bool $signatureOk,
        public readonly bool $ipOk,
        public readonly string $lookupType,
        public readonly string $lookupValue,
        public readonly ?CryptoInvoiceStatus $invoiceStatus = null,
        public readonly ?CryptoPayoutStatus $payoutStatus = null,
        public readonly ?string $txid = null,
        public readonly ?string $amount = null,
        public readonly ?string $currency = null,
        public readonly ?int $confirmations = null,
        public readonly bool $isFinal = false,
        public readonly array $payload = [],
    ) {}
}

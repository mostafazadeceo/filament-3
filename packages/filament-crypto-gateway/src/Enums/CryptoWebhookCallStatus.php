<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Enums;

enum CryptoWebhookCallStatus: string
{
    case Received = 'received';
    case Processing = 'processing';
    case Processed = 'processed';
    case Failed = 'failed';
    case Rejected = 'rejected';
}

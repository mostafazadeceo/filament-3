<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Enums;

enum CryptoInvoicePaymentStatus: string
{
    case Seen = 'seen';
    case Confirming = 'confirming';
    case Confirmed = 'confirmed';
    case Failed = 'failed';
}

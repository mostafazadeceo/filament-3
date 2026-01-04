<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Enums;

enum CryptoPayoutStatus: string
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
}

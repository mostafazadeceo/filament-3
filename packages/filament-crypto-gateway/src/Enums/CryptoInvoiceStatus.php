<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Enums;

enum CryptoInvoiceStatus: string
{
    case Draft = 'draft';
    case Unpaid = 'unpaid';
    case Pending = 'pending';
    case ConfirmCheck = 'confirm_check';
    case Paid = 'paid';
    case PaidOver = 'paid_over';
    case WrongAmount = 'wrong_amount';
    case Completed = 'completed';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
    case RefundProcess = 'refund_process';
    case RefundFailed = 'refund_failed';
    case RefundPaid = 'refund_paid';
}

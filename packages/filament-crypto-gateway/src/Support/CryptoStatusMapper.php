<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Support;

use Haida\FilamentCryptoGateway\Enums\CryptoInvoiceStatus;
use Haida\FilamentCryptoGateway\Enums\CryptoPayoutStatus;

class CryptoStatusMapper
{
    public static function invoiceStatus(string $provider, string|int $status, ?string $additionalStatus = null): CryptoInvoiceStatus
    {
        $normalized = strtolower((string) $status);

        return match ($provider) {
            'cryptomus' => self::mapCryptomusInvoice($normalized),
            'coinbase' => self::mapCoinbaseInvoice($normalized),
            'coinpayments' => self::mapCoinPaymentsInvoice($normalized),
            'btcpay' => self::mapBtcpayInvoice($normalized, $additionalStatus),
            default => CryptoInvoiceStatus::Pending,
        };
    }

    public static function payoutStatus(string $provider, string|int $status): CryptoPayoutStatus
    {
        $normalized = strtolower((string) $status);

        return match ($provider) {
            'cryptomus' => self::mapCryptomusPayout($normalized),
            'coinpayments' => self::mapCoinPaymentsPayout($normalized),
            'btcpay' => CryptoPayoutStatus::Failed,
            default => CryptoPayoutStatus::Processing,
        };
    }

    public static function isFinalInvoiceStatus(CryptoInvoiceStatus $status): bool
    {
        return in_array($status, [
            CryptoInvoiceStatus::Paid,
            CryptoInvoiceStatus::PaidOver,
            CryptoInvoiceStatus::WrongAmount,
            CryptoInvoiceStatus::Completed,
            CryptoInvoiceStatus::Expired,
            CryptoInvoiceStatus::Cancelled,
            CryptoInvoiceStatus::Failed,
            CryptoInvoiceStatus::RefundFailed,
            CryptoInvoiceStatus::RefundPaid,
        ], true);
    }

    public static function isFinalPayoutStatus(CryptoPayoutStatus $status): bool
    {
        return in_array($status, [
            CryptoPayoutStatus::Completed,
            CryptoPayoutStatus::Failed,
            CryptoPayoutStatus::Cancelled,
        ], true);
    }

    protected static function mapCryptomusInvoice(string $status): CryptoInvoiceStatus
    {
        return match ($status) {
            'confirm_check' => CryptoInvoiceStatus::ConfirmCheck,
            'paid' => CryptoInvoiceStatus::Paid,
            'paid_over' => CryptoInvoiceStatus::PaidOver,
            'wrong_amount' => CryptoInvoiceStatus::WrongAmount,
            'cancel', 'cancelled' => CryptoInvoiceStatus::Cancelled,
            'system_fail', 'fail' => CryptoInvoiceStatus::Failed,
            'refund_process' => CryptoInvoiceStatus::RefundProcess,
            'refund_fail' => CryptoInvoiceStatus::RefundFailed,
            'refund_paid' => CryptoInvoiceStatus::RefundPaid,
            default => CryptoInvoiceStatus::Pending,
        };
    }

    protected static function mapCoinbaseInvoice(string $status): CryptoInvoiceStatus
    {
        return match ($status) {
            'charge:created' => CryptoInvoiceStatus::Unpaid,
            'charge:pending' => CryptoInvoiceStatus::Pending,
            'charge:confirmed' => CryptoInvoiceStatus::Paid,
            'charge:failed' => CryptoInvoiceStatus::Failed,
            default => CryptoInvoiceStatus::Pending,
        };
    }

    protected static function mapCoinPaymentsInvoice(string $status): CryptoInvoiceStatus
    {
        return match ($status) {
            '0', '1' => CryptoInvoiceStatus::Pending,
            '2', '100' => CryptoInvoiceStatus::Paid,
            '3' => CryptoInvoiceStatus::PaidOver,
            '-1' => CryptoInvoiceStatus::Cancelled,
            '-2' => CryptoInvoiceStatus::Failed,
            default => CryptoInvoiceStatus::Pending,
        };
    }

    protected static function mapCryptomusPayout(string $status): CryptoPayoutStatus
    {
        return match ($status) {
            'paid', 'completed' => CryptoPayoutStatus::Completed,
            'cancel', 'cancelled' => CryptoPayoutStatus::Cancelled,
            'system_fail', 'refund_fail', 'refund_failed', 'fail' => CryptoPayoutStatus::Failed,
            'confirm_check', 'pending', 'processing' => CryptoPayoutStatus::Processing,
            default => CryptoPayoutStatus::Pending,
        };
    }

    protected static function mapCoinPaymentsPayout(string $status): CryptoPayoutStatus
    {
        return match ($status) {
            '0', '1' => CryptoPayoutStatus::Processing,
            '2', '100' => CryptoPayoutStatus::Completed,
            '-1' => CryptoPayoutStatus::Cancelled,
            '-2' => CryptoPayoutStatus::Failed,
            default => CryptoPayoutStatus::Processing,
        };
    }

    protected static function mapBtcpayInvoice(string $status, ?string $additionalStatus): CryptoInvoiceStatus
    {
        if ($additionalStatus === 'PaidOver') {
            return CryptoInvoiceStatus::PaidOver;
        }

        if ($additionalStatus === 'PaidPartial') {
            return CryptoInvoiceStatus::WrongAmount;
        }

        if ($additionalStatus === 'PaidLate') {
            return CryptoInvoiceStatus::Paid;
        }

        return match ($status) {
            'new' => CryptoInvoiceStatus::Unpaid,
            'processing' => CryptoInvoiceStatus::Pending,
            'paid' => CryptoInvoiceStatus::Paid,
            'settled', 'complete' => CryptoInvoiceStatus::Completed,
            'expired' => CryptoInvoiceStatus::Expired,
            'invalid' => CryptoInvoiceStatus::Failed,
            default => CryptoInvoiceStatus::Pending,
        };
    }
}

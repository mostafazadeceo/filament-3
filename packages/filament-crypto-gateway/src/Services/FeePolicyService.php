<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Services;

use Haida\FilamentCryptoCore\Services\FeePolicyEngine;

class FeePolicyService
{
    public function __construct(protected FeePolicyEngine $engine) {}

    public function calculateInvoiceFee(float $amount, ?int $tenantId = null): array
    {
        $policy = $this->engine->policyForTenant($tenantId);
        $percent = (float) ($policy['fees']['invoice_percent'] ?? 0);
        $fixed = (float) ($policy['fees']['invoice_fixed'] ?? 0);

        $fee = ($amount * $percent / 100) + $fixed;

        return [
            'fee' => round($fee, 8),
            'net' => round(max($amount - $fee, 0), 8),
        ];
    }

    public function calculatePayoutFee(float $amount, ?int $tenantId = null): array
    {
        $policy = $this->engine->policyForTenant($tenantId);
        $percent = (float) ($policy['fees']['payout_percent'] ?? 0);
        $fixed = (float) ($policy['fees']['payout_fixed'] ?? 0);

        $fee = ($amount * $percent / 100) + $fixed;

        return [
            'fee' => round($fee, 8),
            'net' => round(max($amount - $fee, 0), 8),
        ];
    }
}

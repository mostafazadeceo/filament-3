<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\Wallet;
use Filamat\IamSuite\Models\WalletTransaction;
use RuntimeException;

class WalletRiskService
{
    public function __construct(protected SecurityEventService $securityEventService) {}

    public function assertAllowed(Wallet $wallet, string $type, float $amount): void
    {
        if (! (bool) config('filamat-iam.features.wallet_risk_controls', false)) {
            return;
        }

        $config = (array) config('filamat-iam.wallet.risk_controls', []);
        if (! (bool) ($config['enabled'] ?? false)) {
            return;
        }

        $this->checkDailyLimit($wallet, $type, $amount, $config);
        $this->checkVelocity($wallet, $type, $amount, $config);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function checkDailyLimit(Wallet $wallet, string $type, float $amount, array $config): void
    {
        $limit = $config['daily_debit_limit'] ?? null;
        if (! $limit) {
            return;
        }

        if (! in_array($type, ['debit', 'transfer_out', 'capture'], true)) {
            return;
        }

        $sum = WalletTransaction::query()
            ->where('wallet_id', $wallet->getKey())
            ->whereIn('type', ['debit', 'transfer_out', 'capture'])
            ->where('created_at', '>=', now()->subDay())
            ->sum('amount');

        if ($sum + $amount > (float) $limit) {
            $this->securityEventService->record('wallet.risk.daily_limit', 'warning', $wallet->user, $wallet->tenant, [
                'wallet_id' => $wallet->getKey(),
                'amount' => $amount,
            ]);

            throw new RuntimeException('سقف روزانه کیف پول رد شده است.');
        }
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function checkVelocity(Wallet $wallet, string $type, float $amount, array $config): void
    {
        $velocity = (array) ($config['velocity'] ?? []);
        $windowSeconds = (int) ($velocity['window_seconds'] ?? 60);
        $maxCount = $velocity['max_count'] ?? null;
        $maxAmount = $velocity['max_amount'] ?? null;

        if (! $maxCount && ! $maxAmount) {
            return;
        }

        $query = WalletTransaction::query()
            ->where('wallet_id', $wallet->getKey())
            ->where('created_at', '>=', now()->subSeconds($windowSeconds));

        if ($maxCount) {
            $count = (int) $query->count();
            if ($count >= (int) $maxCount) {
                $this->securityEventService->record('wallet.risk.velocity_count', 'warning', $wallet->user, $wallet->tenant, [
                    'wallet_id' => $wallet->getKey(),
                    'count' => $count,
                ]);

                throw new RuntimeException('تعداد تراکنش‌ها در بازه مجاز نیست.');
            }
        }

        if ($maxAmount) {
            $sum = (float) $query->sum('amount');
            if ($sum + $amount > (float) $maxAmount) {
                $this->securityEventService->record('wallet.risk.velocity_amount', 'warning', $wallet->user, $wallet->tenant, [
                    'wallet_id' => $wallet->getKey(),
                    'amount' => $amount,
                ]);

                throw new RuntimeException('محدودیت حجم تراکنش‌ها رد شده است.');
            }
        }
    }
}

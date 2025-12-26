<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\Wallet;
use Filamat\IamSuite\Models\WalletHold;
use Filamat\IamSuite\Models\WalletTransaction;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use RuntimeException;

class WalletService
{
    public function __construct(
        protected DatabaseManager $db,
        protected WalletRiskService $riskService
    ) {}

    public function createWallet(Authenticatable $user, Tenant $tenant, string $currency): Wallet
    {
        return Wallet::query()->firstOrCreate([
            'tenant_id' => $tenant->getKey(),
            'user_id' => $user->getAuthIdentifier(),
            'currency' => strtolower($currency),
        ], [
            'balance' => 0,
            'status' => 'active',
        ]);
    }

    public function credit(Wallet $wallet, float $amount, string $idempotencyKey, array $meta = []): WalletTransaction
    {
        return $this->recordTransaction($wallet, 'credit', $amount, $idempotencyKey, $meta);
    }

    public function debit(Wallet $wallet, float $amount, string $idempotencyKey, array $meta = []): WalletTransaction
    {
        return $this->recordTransaction($wallet, 'debit', $amount, $idempotencyKey, $meta);
    }

    public function hold(Wallet $wallet, float $amount, string $reason, array $meta = [], ?string $idempotencyKey = null): WalletHold
    {
        return $this->db->transaction(function () use ($wallet, $amount, $reason, $meta, $idempotencyKey) {
            if ($idempotencyKey) {
                $existing = WalletHold::query()
                    ->where('wallet_id', $wallet->getKey())
                    ->where('idempotency_key', $idempotencyKey)
                    ->first();

                if ($existing) {
                    return $existing;
                }
            }

            $this->riskService->assertAllowed($wallet, 'debit', $amount);

            $wallet = $wallet->newQuery()->lockForUpdate()->findOrFail($wallet->getKey());

            if ($wallet->balance < $amount) {
                throw new RuntimeException('موجودی کافی نیست.');
            }

            $wallet->balance -= $amount;
            $wallet->save();

            return WalletHold::query()->create([
                'wallet_id' => $wallet->getKey(),
                'amount' => $amount,
                'idempotency_key' => $idempotencyKey,
                'reason' => $reason,
                'status' => 'active',
                'meta' => $meta,
            ]);
        });
    }

    public function captureHold(WalletHold $hold, string $idempotencyKey, array $meta = []): WalletTransaction
    {
        return $this->db->transaction(function () use ($hold, $idempotencyKey, $meta) {
            $hold = $hold->newQuery()->lockForUpdate()->findOrFail($hold->getKey());

            if ($hold->status !== 'active') {
                throw new RuntimeException('وضعیت هولد معتبر نیست.');
            }

            $transaction = $this->recordTransaction($hold->wallet, 'capture', (float) $hold->amount, $idempotencyKey, $meta, false);
            $hold->update(['status' => 'captured']);

            return $transaction;
        });
    }

    public function releaseHold(WalletHold $hold, string $idempotencyKey, array $meta = []): WalletTransaction
    {
        return $this->db->transaction(function () use ($hold, $idempotencyKey, $meta) {
            $hold = $hold->newQuery()->lockForUpdate()->findOrFail($hold->getKey());

            if ($hold->status !== 'active') {
                throw new RuntimeException('وضعیت هولد معتبر نیست.');
            }

            $transaction = $this->recordTransaction($hold->wallet, 'release', (float) $hold->amount, $idempotencyKey, $meta, true);
            $hold->update(['status' => 'released']);

            return $transaction;
        });
    }

    public function transfer(Wallet $from, Wallet $to, float $amount, string $idempotencyKey, array $meta = []): array
    {
        if ($from->currency !== $to->currency) {
            throw new RuntimeException('واحد کیف پول‌ها باید یکسان باشد.');
        }

        return $this->db->transaction(function () use ($from, $to, $amount, $idempotencyKey, $meta) {
            $from = $from->newQuery()->lockForUpdate()->findOrFail($from->getKey());
            $to = $to->newQuery()->lockForUpdate()->findOrFail($to->getKey());

            if ($from->balance < $amount) {
                throw new RuntimeException('موجودی کافی نیست.');
            }

            $debit = $this->recordTransaction($from, 'transfer_out', $amount, $idempotencyKey.':out', $meta, false);
            $credit = $this->recordTransaction($to, 'transfer_in', $amount, $idempotencyKey.':in', $meta, false);

            return ['debit' => $debit, 'credit' => $credit];
        });
    }

    protected function recordTransaction(Wallet $wallet, string $type, float $amount, string $idempotencyKey, array $meta = [], bool $applyBalance = true): WalletTransaction
    {
        $existing = WalletTransaction::query()
            ->where('wallet_id', $wallet->getKey())
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        if ($existing) {
            return $existing;
        }

        $this->riskService->assertAllowed($wallet, $type, $amount);

        return $this->db->transaction(function () use ($wallet, $type, $amount, $idempotencyKey, $meta, $applyBalance) {
            $wallet = $wallet->newQuery()->lockForUpdate()->findOrFail($wallet->getKey());

            if ($applyBalance) {
                if (in_array($type, ['debit', 'transfer_out'], true)) {
                    if ($wallet->balance < $amount) {
                        throw new RuntimeException('موجودی کافی نیست.');
                    }
                    $wallet->balance -= $amount;
                } else {
                    $wallet->balance += $amount;
                }

                $wallet->save();
            }

            return WalletTransaction::query()->create([
                'wallet_id' => $wallet->getKey(),
                'type' => $type,
                'amount' => $amount,
                'status' => 'posted',
                'idempotency_key' => $idempotencyKey,
                'meta' => Arr::wrap($meta),
            ]);
        });
    }
}

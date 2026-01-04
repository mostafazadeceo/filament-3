<?php

namespace Haida\FilamentCryptoCore\Services;

use Haida\FilamentCryptoCore\Models\CryptoAccount;
use Haida\FilamentCryptoCore\Models\CryptoLedger;
use Haida\FilamentCryptoCore\Models\CryptoLedgerEntry;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

class LedgerService
{
    public function __construct(protected DatabaseManager $db) {}

    /**
     * @param  array<int, array<string, mixed>>  $entries
     */
    public function postLedger(array $payload, array $entries): CryptoLedger
    {
        return $this->db->transaction(function () use ($payload, $entries) {
            $ledger = CryptoLedger::query()->create([
                'tenant_id' => $payload['tenant_id'] ?? null,
                'ref_type' => $payload['ref_type'] ?? null,
                'ref_id' => $payload['ref_id'] ?? null,
                'occurred_at' => $payload['occurred_at'] ?? now(),
                'description' => $payload['description'] ?? null,
                'meta' => $payload['meta'] ?? null,
            ]);

            $this->persistEntries($ledger, $entries);

            return $ledger->refresh();
        });
    }

    public function alreadyRecorded(string $refType, int|string $refId, int $tenantId): bool
    {
        return CryptoLedger::query()
            ->where('tenant_id', $tenantId)
            ->where('ref_type', $refType)
            ->where('ref_id', (string) $refId)
            ->exists();
    }

    /**
     * @param  array<int, array<string, mixed>>  $entries
     */
    public function record(string $refType, int|string $refId, int $tenantId, string $description, array $entries, $occurredAt = null, array $meta = []): CryptoLedger
    {
        if ($this->alreadyRecorded($refType, $refId, $tenantId)) {
            return CryptoLedger::query()
                ->where('tenant_id', $tenantId)
                ->where('ref_type', $refType)
                ->where('ref_id', (string) $refId)
                ->firstOrFail();
        }

        return $this->postLedger([
            'tenant_id' => $tenantId,
            'ref_type' => $refType,
            'ref_id' => (string) $refId,
            'occurred_at' => $occurredAt ?? now(),
            'description' => $description,
            'meta' => $meta,
        ], $entries);
    }

    /**
     * @param  array<int, array<string, mixed>>  $entries
     */
    protected function persistEntries(CryptoLedger $ledger, array $entries): void
    {
        $precision = (int) config('filament-crypto-core.ledger.precision', 8);
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach ($entries as $entry) {
            $debit = (float) ($entry['debit'] ?? 0);
            $credit = (float) ($entry['credit'] ?? 0);
            $totalDebit += $debit;
            $totalCredit += $credit;

            $accountId = $entry['account_id'] ?? null;
            if (! $accountId && isset($entry['account_code'])) {
                $accountId = $this->resolveAccountId($ledger->tenant_id, (string) $entry['account_code']);
            }

            if (! $accountId) {
                throw ValidationException::withMessages(['account_id' => 'حساب دفترکل معتبر نیست.']);
            }

            CryptoLedgerEntry::query()->create([
                'ledger_id' => $ledger->getKey(),
                'tenant_id' => $ledger->tenant_id,
                'account_id' => $accountId,
                'debit' => round($debit, $precision),
                'credit' => round($credit, $precision),
                'currency' => $entry['currency'] ?? config('filament-crypto-core.defaults.currency', 'USDT'),
                'meta' => $entry['meta'] ?? null,
            ]);
        }

        if (round($totalDebit, $precision) !== round($totalCredit, $precision)) {
            throw ValidationException::withMessages(['ledger' => 'عدم توازن در دفترکل.']);
        }
    }

    protected function resolveAccountId(int $tenantId, string $code): ?int
    {
        $account = CryptoAccount::query()
            ->where('tenant_id', $tenantId)
            ->where('code', $code)
            ->first();

        if ($account) {
            return $account->getKey();
        }

        $defaults = (array) config('filament-crypto-core.ledger.default_accounts', []);
        $default = null;
        foreach ($defaults as $item) {
            if (is_array($item) && ($item['code'] ?? null) === $code) {
                $default = $item;
                break;
            }
        }

        if (! $default) {
            return null;
        }

        $created = CryptoAccount::query()->create([
            'tenant_id' => $tenantId,
            'code' => $code,
            'name_fa' => $default['name_fa'] ?? $code,
            'type' => $default['type'] ?? 'asset',
        ]);

        return $created->getKey();
    }
}

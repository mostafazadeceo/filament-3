<?php

namespace Haida\FilamentLoyaltyClub\Services;

use Carbon\CarbonInterface;
use Haida\FilamentLoyaltyClub\Contracts\WalletAdapterInterface;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyEvent;
use Haida\FilamentLoyaltyClub\Models\LoyaltyPointsBucket;
use Haida\FilamentLoyaltyClub\Models\LoyaltyPointsConsumption;
use Haida\FilamentLoyaltyClub\Models\LoyaltyWalletAccount;
use Haida\FilamentLoyaltyClub\Models\LoyaltyWalletLedger;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use RuntimeException;

class LoyaltyLedgerService
{
    public function __construct(
        protected DatabaseManager $db,
        protected WalletAdapterInterface $cashbackAdapter,
        protected LoyaltyAuditService $auditService,
    ) {}

    public function getOrCreateAccount(LoyaltyCustomer $customer): LoyaltyWalletAccount
    {
        return LoyaltyWalletAccount::query()->firstOrCreate([
            'tenant_id' => $customer->tenant_id,
            'customer_id' => $customer->getKey(),
        ], [
            'points_balance' => 0,
            'points_earned_total' => 0,
            'points_redeemed_total' => 0,
            'cashback_balance' => 0,
            'cashback_earned_total' => 0,
            'cashback_redeemed_total' => 0,
        ]);
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function creditPoints(
        LoyaltyCustomer $customer,
        int $points,
        string $idempotencyKey,
        array $meta = [],
        ?CarbonInterface $expiresAt = null,
        ?LoyaltyEvent $event = null,
        ?array $reference = null,
    ): LoyaltyWalletLedger {
        if ($points <= 0) {
            throw new RuntimeException('امتیاز نامعتبر است.');
        }

        $existing = $this->findLedgerByIdempotency($customer, $idempotencyKey);
        if ($existing) {
            return $existing;
        }

        return $this->db->transaction(function () use ($customer, $points, $idempotencyKey, $meta, $expiresAt, $event, $reference) {
            $account = $this->lockAccount($customer);
            $account->points_balance += $points;
            $account->points_earned_total += $points;
            $account->save();

            $ledger = LoyaltyWalletLedger::query()->create([
                'tenant_id' => $customer->tenant_id,
                'customer_id' => $customer->getKey(),
                'event_id' => $event?->getKey(),
                'type' => 'earn',
                'points_delta' => $points,
                'cashback_delta' => 0,
                'balance_after_points' => $account->points_balance,
                'balance_after_cashback' => $account->cashback_balance,
                'status' => 'posted',
                'idempotency_key' => $idempotencyKey,
                'reference_type' => $reference['type'] ?? null,
                'reference_id' => $reference['id'] ?? null,
                'meta' => Arr::wrap($meta),
                'expires_at' => $expiresAt,
            ]);

            LoyaltyPointsBucket::query()->create([
                'tenant_id' => $customer->tenant_id,
                'customer_id' => $customer->getKey(),
                'ledger_id' => $ledger->getKey(),
                'points_total' => $points,
                'points_available' => $points,
                'expires_at' => $expiresAt,
            ]);

            return $ledger;
        });
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function debitPoints(
        LoyaltyCustomer $customer,
        int $points,
        string $idempotencyKey,
        array $meta = [],
        ?LoyaltyEvent $event = null,
        ?array $reference = null,
    ): LoyaltyWalletLedger {
        if ($points <= 0) {
            throw new RuntimeException('امتیاز نامعتبر است.');
        }

        $existing = $this->findLedgerByIdempotency($customer, $idempotencyKey);
        if ($existing) {
            return $existing;
        }

        return $this->db->transaction(function () use ($customer, $points, $idempotencyKey, $meta, $event, $reference) {
            $account = $this->lockAccount($customer);
            if ($account->points_balance < $points) {
                throw new RuntimeException('امتیاز کافی نیست.');
            }

            $remaining = $points;
            $consumptions = [];
            $buckets = LoyaltyPointsBucket::query()
                ->where('tenant_id', $customer->tenant_id)
                ->where('customer_id', $customer->getKey())
                ->where('points_available', '>', 0)
                ->orderByRaw('expires_at is null')
                ->orderBy('expires_at')
                ->lockForUpdate()
                ->get();

            foreach ($buckets as $bucket) {
                if ($remaining <= 0) {
                    break;
                }

                $use = min($remaining, $bucket->points_available);
                if ($use <= 0) {
                    continue;
                }

                $bucket->points_available -= $use;
                $bucket->save();

                $consumptions[] = [
                    'tenant_id' => $customer->tenant_id,
                    'customer_id' => $customer->getKey(),
                    'bucket_id' => $bucket->getKey(),
                    'points_used' => $use,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $remaining -= $use;
            }

            if ($remaining > 0) {
                throw new RuntimeException('امتیاز کافی برای مصرف وجود ندارد.');
            }

            $account->points_balance -= $points;
            $account->points_redeemed_total += $points;
            $account->save();

            $ledger = LoyaltyWalletLedger::query()->create([
                'tenant_id' => $customer->tenant_id,
                'customer_id' => $customer->getKey(),
                'event_id' => $event?->getKey(),
                'type' => 'burn',
                'points_delta' => -$points,
                'cashback_delta' => 0,
                'balance_after_points' => $account->points_balance,
                'balance_after_cashback' => $account->cashback_balance,
                'status' => 'posted',
                'idempotency_key' => $idempotencyKey,
                'reference_type' => $reference['type'] ?? null,
                'reference_id' => $reference['id'] ?? null,
                'meta' => Arr::wrap($meta),
            ]);

            foreach ($consumptions as &$row) {
                $row['ledger_id'] = $ledger->getKey();
            }
            unset($row);

            if ($consumptions !== []) {
                LoyaltyPointsConsumption::query()->insert($consumptions);
            }

            return $ledger;
        });
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function creditCashback(
        LoyaltyCustomer $customer,
        float $amount,
        string $idempotencyKey,
        array $meta = [],
        ?LoyaltyEvent $event = null,
        ?array $reference = null,
    ): LoyaltyWalletLedger {
        if ($amount <= 0) {
            throw new RuntimeException('مبلغ نامعتبر است.');
        }

        if (! (bool) config('filament-loyalty-club.features.cashback.enabled', false)) {
            throw new RuntimeException('کش‌بک غیرفعال است.');
        }

        $existing = $this->findLedgerByIdempotency($customer, $idempotencyKey);
        if ($existing) {
            return $existing;
        }

        return $this->db->transaction(function () use ($customer, $amount, $idempotencyKey, $meta, $event, $reference) {
            $account = $this->lockAccount($customer);
            $account->cashback_balance += $amount;
            $account->cashback_earned_total += $amount;
            $account->save();

            $this->cashbackAdapter->credit($customer, $amount, $idempotencyKey, $meta);

            return LoyaltyWalletLedger::query()->create([
                'tenant_id' => $customer->tenant_id,
                'customer_id' => $customer->getKey(),
                'event_id' => $event?->getKey(),
                'type' => 'cashback_credit',
                'points_delta' => 0,
                'cashback_delta' => $amount,
                'balance_after_points' => $account->points_balance,
                'balance_after_cashback' => $account->cashback_balance,
                'status' => 'posted',
                'idempotency_key' => $idempotencyKey,
                'reference_type' => $reference['type'] ?? null,
                'reference_id' => $reference['id'] ?? null,
                'meta' => Arr::wrap($meta),
            ]);
        });
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function debitCashback(
        LoyaltyCustomer $customer,
        float $amount,
        string $idempotencyKey,
        array $meta = [],
        ?LoyaltyEvent $event = null,
        ?array $reference = null,
    ): LoyaltyWalletLedger {
        if ($amount <= 0) {
            throw new RuntimeException('مبلغ نامعتبر است.');
        }

        if (! (bool) config('filament-loyalty-club.features.cashback.enabled', false)) {
            throw new RuntimeException('کش‌بک غیرفعال است.');
        }

        $existing = $this->findLedgerByIdempotency($customer, $idempotencyKey);
        if ($existing) {
            return $existing;
        }

        return $this->db->transaction(function () use ($customer, $amount, $idempotencyKey, $meta, $event, $reference) {
            $account = $this->lockAccount($customer);
            if ($account->cashback_balance < $amount) {
                throw new RuntimeException('اعتبار کافی نیست.');
            }

            $account->cashback_balance -= $amount;
            $account->cashback_redeemed_total += $amount;
            $account->save();

            $this->cashbackAdapter->debit($customer, $amount, $idempotencyKey, $meta);

            return LoyaltyWalletLedger::query()->create([
                'tenant_id' => $customer->tenant_id,
                'customer_id' => $customer->getKey(),
                'event_id' => $event?->getKey(),
                'type' => 'cashback_debit',
                'points_delta' => 0,
                'cashback_delta' => -$amount,
                'balance_after_points' => $account->points_balance,
                'balance_after_cashback' => $account->cashback_balance,
                'status' => 'posted',
                'idempotency_key' => $idempotencyKey,
                'reference_type' => $reference['type'] ?? null,
                'reference_id' => $reference['id'] ?? null,
                'meta' => Arr::wrap($meta),
            ]);
        });
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function reverseLedger(LoyaltyWalletLedger $ledger, string $idempotencyKey, array $meta = []): LoyaltyWalletLedger
    {
        $customer = $ledger->customer;
        if (! $customer) {
            throw new RuntimeException('مشتری یافت نشد.');
        }

        $existing = $this->findLedgerByIdempotency($customer, $idempotencyKey);
        if ($existing) {
            return $existing;
        }

        return $this->db->transaction(function () use ($ledger, $customer, $idempotencyKey, $meta) {
            $account = $this->lockAccount($customer);

            $pointsDelta = -$ledger->points_delta;
            $cashbackDelta = -((float) $ledger->cashback_delta);
            $appliedPointsDelta = $pointsDelta;
            $pointsUnderflow = 0;

            if ($pointsDelta < 0 && ($account->points_balance + $pointsDelta) < 0) {
                $pointsUnderflow = abs($account->points_balance + $pointsDelta);
                $appliedPointsDelta = -$account->points_balance;
            }

            $account->points_balance += $appliedPointsDelta;
            $account->cashback_balance += $cashbackDelta;

            if ($ledger->points_delta > 0) {
                $account->points_earned_total = max(0, $account->points_earned_total - (int) $ledger->points_delta);
            } elseif ($ledger->points_delta < 0) {
                $account->points_redeemed_total = max(0, $account->points_redeemed_total - abs((int) $ledger->points_delta));
            }

            if ($ledger->cashback_delta > 0) {
                $account->cashback_earned_total = max(0, (float) $account->cashback_earned_total - (float) $ledger->cashback_delta);
            } elseif ($ledger->cashback_delta < 0) {
                $account->cashback_redeemed_total = max(0, (float) $account->cashback_redeemed_total - abs((float) $ledger->cashback_delta));
            }
            $account->save();

            if ($ledger->points_delta > 0) {
                $bucket = LoyaltyPointsBucket::query()
                    ->where('tenant_id', $customer->tenant_id)
                    ->where('ledger_id', $ledger->getKey())
                    ->lockForUpdate()
                    ->first();

                if ($bucket) {
                    $bucket->points_total = 0;
                    $bucket->points_available = 0;
                    $bucket->save();
                }
            } elseif ($ledger->points_delta < 0) {
                $restorePoints = abs($ledger->points_delta);
                $expiryDays = (int) config('filament-loyalty-club.points.expiry.default_days', 365);
                $expiresAt = $expiryDays > 0 ? now()->addDays($expiryDays) : null;

                LoyaltyPointsBucket::query()->create([
                    'tenant_id' => $customer->tenant_id,
                    'customer_id' => $customer->getKey(),
                    'ledger_id' => $ledger->getKey(),
                    'points_total' => $restorePoints,
                    'points_available' => $restorePoints,
                    'expires_at' => $expiresAt,
                ]);
            }

            if ($ledger->cashback_delta > 0) {
                $this->cashbackAdapter->debit($customer, (float) $ledger->cashback_delta, $idempotencyKey.':cashback', $meta);
            } elseif ($ledger->cashback_delta < 0) {
                $this->cashbackAdapter->credit($customer, abs((float) $ledger->cashback_delta), $idempotencyKey.':cashback', $meta);
            }

            $auditMeta = [
                'ledger_id' => $ledger->getKey(),
                'points_delta' => (int) $ledger->points_delta,
                'cashback_delta' => (float) $ledger->cashback_delta,
            ];

            if ($pointsUnderflow > 0) {
                $auditMeta['points_underflow'] = $pointsUnderflow;
            }

            $this->auditService->record('ledger_reversed', $auditMeta, $ledger);

            return LoyaltyWalletLedger::query()->create([
                'tenant_id' => $customer->tenant_id,
                'customer_id' => $customer->getKey(),
                'event_id' => $ledger->event_id,
                'type' => 'reversal',
                'points_delta' => $appliedPointsDelta,
                'cashback_delta' => $cashbackDelta,
                'balance_after_points' => $account->points_balance,
                'balance_after_cashback' => $account->cashback_balance,
                'status' => 'posted',
                'idempotency_key' => $idempotencyKey,
                'reference_type' => $ledger->reference_type,
                'reference_id' => $ledger->reference_id,
                'reversal_of_id' => $ledger->getKey(),
                'meta' => Arr::wrap(array_merge($meta, $pointsUnderflow > 0 ? ['points_underflow' => $pointsUnderflow] : [])),
            ]);
        });
    }

    protected function lockAccount(LoyaltyCustomer $customer): LoyaltyWalletAccount
    {
        $account = LoyaltyWalletAccount::query()
            ->where('tenant_id', $customer->tenant_id)
            ->where('customer_id', $customer->getKey())
            ->lockForUpdate()
            ->first();

        if ($account) {
            return $account;
        }

        return $this->getOrCreateAccount($customer);
    }

    protected function findLedgerByIdempotency(LoyaltyCustomer $customer, string $idempotencyKey): ?LoyaltyWalletLedger
    {
        if ($idempotencyKey === '') {
            return null;
        }

        return LoyaltyWalletLedger::query()
            ->where('tenant_id', $customer->tenant_id)
            ->where('idempotency_key', $idempotencyKey)
            ->first();
    }
}

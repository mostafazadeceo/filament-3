<?php

namespace Haida\FilamentLoyaltyClub\Services;

use Carbon\CarbonImmutable;
use Haida\FilamentLoyaltyClub\Contracts\PurchaseAdapterInterface;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyEvent;
use Haida\FilamentLoyaltyClub\Models\LoyaltyPointsBucket;
use Haida\FilamentLoyaltyClub\Models\LoyaltyPointsRule;
use Haida\FilamentLoyaltyClub\Models\LoyaltyWalletLedger;
use Illuminate\Support\Arr;
use RuntimeException;

class LoyaltyRuleEngine
{
    public function __construct(
        protected LoyaltyLedgerService $ledgerService,
        protected LoyaltyTierService $tierService,
        protected LoyaltyReferralService $referralService,
        protected LoyaltyMissionService $missionService,
        protected LoyaltyMetricsService $metricsService,
        protected PurchaseAdapterInterface $purchaseAdapter,
        protected LoyaltyAuditService $auditService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function process(LoyaltyEvent $event): array
    {
        $customer = $event->customer;
        if (! $customer) {
            throw new RuntimeException('مشتری برای رویداد مشخص نیست.');
        }

        $this->assertSourceAllowed($event);

        $result = [
            'points_awarded' => 0,
            'cashback_awarded' => 0,
            'tier_changed' => false,
        ];

        $payload = Arr::wrap($event->payload);

        if ($event->type === 'refund' || $event->type === 'reversal') {
            $this->processReversal($event, $payload);

            return $result;
        }

        if ($event->type === 'manual_adjustment') {
            $result['points_awarded'] += $this->processManualAdjustment($event, $payload, $customer);

            return $result;
        }

        if ($event->type === 'purchase_completed') {
            $purchase = $this->purchaseAdapter->resolve($payload);
            $payload['purchase_amount'] = $purchase->amount;
            $payload['purchase_currency'] = $purchase->currency;
            $payload['purchase_reference'] = $purchase->reference;
            $payload['occurred_at'] = $purchase->occurredAt?->toDateTimeString();

            $this->metricsService->recordPurchase($customer, $purchase->amount, $purchase->occurredAt);
        }

        $rules = LoyaltyPointsRule::query()
            ->where('tenant_id', $customer->tenant_id)
            ->where('event_type', $event->type)
            ->where('status', 'active')
            ->orderBy('priority')
            ->get();

        foreach ($rules as $rule) {
            $points = $this->evaluatePointsRule($event, $rule, $payload, $customer);
            if ($points <= 0) {
                continue;
            }

            $idempotencyKey = $this->ruleIdempotencyKey($event, $rule);
            $expiresAt = $this->resolveExpiry($event);
            $ledger = $this->ledgerService->creditPoints(
                $customer,
                $points,
                $idempotencyKey,
                ['rule_id' => $rule->getKey(), 'event_type' => $event->type],
                $expiresAt,
                $event,
                ['type' => 'rule', 'id' => $rule->getKey()]
            );

            $result['points_awarded'] += $points;
            $result['last_ledger_id'] = $ledger->getKey();
        }

        $this->missionService->handleEvent($event, $payload);
        $this->referralService->handleEvent($event, $payload);

        $tierChanged = $this->tierService->syncTier($customer, $payload);
        $result['tier_changed'] = $tierChanged;

        return $result;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function processReversal(LoyaltyEvent $event, array $payload): void
    {
        $ledgerId = $payload['ledger_id'] ?? null;
        $referenceKey = $payload['ledger_idempotency_key'] ?? null;
        $customer = $event->customer;
        if (! $customer) {
            return;
        }

        $ledger = null;
        if ($ledgerId) {
            $ledger = LoyaltyWalletLedger::query()
                ->where('tenant_id', $customer->tenant_id)
                ->where('customer_id', $customer->getKey())
                ->find($ledgerId);
        } elseif ($referenceKey) {
            $ledger = LoyaltyWalletLedger::query()
                ->where('tenant_id', $customer->tenant_id)
                ->where('customer_id', $customer->getKey())
                ->where('idempotency_key', $referenceKey)
                ->first();
        }

        if ($ledger) {
            $this->ledgerService->reverseLedger($ledger, $event->idempotency_key ?: 'reversal:'.$event->getKey(), [
                'reason' => $payload['reason'] ?? null,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function processManualAdjustment(LoyaltyEvent $event, array $payload, LoyaltyCustomer $customer): int
    {
        $points = (int) ($payload['points_delta'] ?? 0);
        $cashback = (float) ($payload['cashback_delta'] ?? 0);
        $idempotencyKey = $event->idempotency_key ?: 'manual:'.$event->getKey();

        if ($points > 0) {
            $this->ledgerService->creditPoints($customer, $points, $idempotencyKey.':points', ['manual' => true], $this->resolveExpiry($event), $event);
        } elseif ($points < 0) {
            $this->ledgerService->debitPoints($customer, abs($points), $idempotencyKey.':points', ['manual' => true], $event);
        }

        if ($cashback > 0) {
            $this->ledgerService->creditCashback($customer, $cashback, $idempotencyKey.':cashback', ['manual' => true], $event);
        } elseif ($cashback < 0) {
            $this->ledgerService->debitCashback($customer, abs($cashback), $idempotencyKey.':cashback', ['manual' => true], $event);
        }

        if ($points !== 0 || $cashback !== 0.0) {
            $this->auditService->record('manual_adjustment', [
                'event_id' => $event->getKey(),
                'points_delta' => $points,
                'cashback_delta' => $cashback,
            ], $customer);
        }

        return $points;
    }

    protected function evaluatePointsRule(LoyaltyEvent $event, LoyaltyPointsRule $rule, array $payload, LoyaltyCustomer $customer): int
    {
        $now = CarbonImmutable::now();
        if ($rule->valid_from && $now->lt($rule->valid_from)) {
            return 0;
        }
        if ($rule->valid_until && $now->gt($rule->valid_until)) {
            return 0;
        }

        if (! $this->matchesScope($rule, $payload)) {
            return 0;
        }

        $amount = (float) ($payload['purchase_amount'] ?? $payload['amount'] ?? 0);
        if ($rule->min_amount && $amount < (float) $rule->min_amount) {
            return 0;
        }

        $points = 0;
        if ($rule->points_type === 'percent') {
            $rate = (float) ($rule->percent_rate ?? 0);
            $points = (int) round($amount * $rate);
        } else {
            $points = (int) $rule->points_value;
        }

        if ($rule->max_points && $points > $rule->max_points) {
            $points = (int) $rule->max_points;
        }

        $points = $this->applyCaps($customer, $rule, $points);

        return max(0, $points);
    }

    protected function applyCaps(LoyaltyCustomer $customer, LoyaltyPointsRule $rule, int $points): int
    {
        if ($points <= 0) {
            return 0;
        }

        $dailyCap = (int) config('filament-loyalty-club.points.caps.daily', 0);
        if ($dailyCap > 0) {
            $dailyTotal = LoyaltyWalletLedger::query()
                ->where('tenant_id', $customer->tenant_id)
                ->where('customer_id', $customer->getKey())
                ->where('type', 'earn')
                ->where('created_at', '>=', now()->subDay())
                ->sum('points_delta');
            $remaining = $dailyCap - (int) $dailyTotal;
            $points = min($points, max(0, $remaining));
        }

        $weeklyCap = (int) config('filament-loyalty-club.points.caps.weekly', 0);
        if ($weeklyCap > 0) {
            $weeklyTotal = LoyaltyWalletLedger::query()
                ->where('tenant_id', $customer->tenant_id)
                ->where('customer_id', $customer->getKey())
                ->where('type', 'earn')
                ->where('created_at', '>=', now()->subWeek())
                ->sum('points_delta');
            $remaining = $weeklyCap - (int) $weeklyTotal;
            $points = min($points, max(0, $remaining));
        }

        if ($rule->cap_period && $rule->cap_count) {
            $since = match ($rule->cap_period) {
                'daily' => now()->subDay(),
                'weekly' => now()->subWeek(),
                'monthly' => now()->subMonth(),
                default => null,
            };
            if ($since) {
                $ruleTotal = LoyaltyWalletLedger::query()
                    ->where('tenant_id', $customer->tenant_id)
                    ->where('customer_id', $customer->getKey())
                    ->where('type', 'earn')
                    ->where('created_at', '>=', $since)
                    ->where('reference_type', 'rule')
                    ->where('reference_id', $rule->getKey())
                    ->sum('points_delta');

                $remaining = (int) $rule->cap_count - (int) $ruleTotal;
                $points = min($points, max(0, $remaining));
            }
        }

        return $points;
    }

    protected function matchesScope(LoyaltyPointsRule $rule, array $payload): bool
    {
        if ($rule->scope_type === 'global') {
            return true;
        }

        $scopeRef = (string) ($rule->scope_ref ?? '');
        if ($scopeRef === '') {
            return true;
        }

        $payloadScope = $payload['scope_ref'] ?? $payload[$rule->scope_type.'_id'] ?? null;

        return (string) $payloadScope === $scopeRef;
    }

    protected function assertSourceAllowed(LoyaltyEvent $event): void
    {
        $allowed = array_filter((array) config('filament-loyalty-club.events.allowed_sources', []));
        if (! $allowed) {
            return;
        }

        $source = (string) ($event->source ?? '');
        if ($source !== '' && ! in_array($source, $allowed, true)) {
            throw new RuntimeException('منبع رویداد مجاز نیست.');
        }
    }

    protected function ruleIdempotencyKey(LoyaltyEvent $event, LoyaltyPointsRule $rule): string
    {
        if ($event->idempotency_key) {
            return $event->idempotency_key.':rule:'.$rule->getKey();
        }

        return 'event:'.$event->getKey().':rule:'.$rule->getKey();
    }

    protected function resolveExpiry(LoyaltyEvent $event): ?CarbonImmutable
    {
        $strategy = (string) config('filament-loyalty-club.points.expiry.strategy', 'fixed');
        $days = (int) config('filament-loyalty-club.points.expiry.default_days', 365);

        if ($strategy === 'fixed' && $days > 0) {
            return CarbonImmutable::now()->addDays($days);
        }

        if ($strategy === 'inactivity' && $days > 0) {
            $expiresAt = CarbonImmutable::now()->addDays($days);
            $customer = $event->customer;
            if ($customer) {
                LoyaltyPointsBucket::query()
                    ->where('tenant_id', $customer->tenant_id)
                    ->where('customer_id', $customer->getKey())
                    ->where('points_available', '>', 0)
                    ->where(function ($query) {
                        $query->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    })
                    ->update(['expires_at' => $expiresAt]);
            }

            return $expiresAt;
        }

        return null;
    }
}

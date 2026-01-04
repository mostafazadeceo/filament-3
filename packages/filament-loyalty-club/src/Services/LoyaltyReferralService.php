<?php

namespace Haida\FilamentLoyaltyClub\Services;

use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyEvent;
use Haida\FilamentLoyaltyClub\Models\LoyaltyFraudSignal;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReferral;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReferralProgram;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class LoyaltyReferralService
{
    public function __construct(
        protected LoyaltyLedgerService $ledgerService,
        protected LoyaltyAuditService $auditService,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function createReferral(LoyaltyReferralProgram $program, LoyaltyCustomer $referrer, array $payload): LoyaltyReferral
    {
        $referralCode = $this->generateCode($program, $referrer);

        $referral = LoyaltyReferral::query()->create([
            'tenant_id' => $referrer->tenant_id,
            'program_id' => $program->getKey(),
            'referrer_customer_id' => $referrer->getKey(),
            'referee_phone' => $payload['referee_phone'] ?? null,
            'referee_email' => $payload['referee_email'] ?? null,
            'referral_code' => $referralCode,
            'status' => 'pending',
            'meta' => Arr::only($payload, ['source', 'campaign_id']),
        ]);

        if ($this->isSelfReferral($referrer, $referral)) {
            $this->flagReferral($referral, 'self_referral', 'matching_identity');
        }

        return $referral;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function handleEvent(LoyaltyEvent $event, array $payload): void
    {
        if ($event->type === 'referral_completed') {
            $this->attachReferral($event, $payload);

            return;
        }

        if ($event->type !== 'purchase_completed') {
            return;
        }

        $customer = $event->customer;
        if (! $customer) {
            return;
        }

        $referral = LoyaltyReferral::query()
            ->where('tenant_id', $customer->tenant_id)
            ->where('referee_customer_id', $customer->getKey())
            ->where('status', 'pending')
            ->first();

        if (! $referral) {
            return;
        }

        $program = $referral->program;
        if (! $program || $program->status !== 'active') {
            return;
        }

        $amount = (float) ($payload['purchase_amount'] ?? $payload['amount'] ?? 0);
        if ($program->min_purchase_amount && $amount < (float) $program->min_purchase_amount) {
            return;
        }

        if ($this->exceedsReferralLimits($program, $referral)) {
            $this->flagReferral($referral, 'rate_limit', 'referrer_limit');

            return;
        }

        $referral->status = 'qualified';
        $referral->qualified_at = now();
        $referral->reward_due_at = now()->addDays((int) $program->waiting_days);
        $referral->save();

        if ((int) $program->waiting_days <= 0) {
            $this->rewardReferral($referral, $program);
        }
    }

    protected function attachReferral(LoyaltyEvent $event, array $payload): void
    {
        $customer = $event->customer;
        if (! $customer) {
            return;
        }

        $code = (string) ($payload['referral_code'] ?? '');
        if ($code === '') {
            return;
        }

        $referral = LoyaltyReferral::query()
            ->where('tenant_id', $customer->tenant_id)
            ->where('referral_code', $code)
            ->first();

        if (! $referral || $referral->referee_customer_id) {
            return;
        }

        $referral->referee_customer_id = $customer->getKey();
        $referral->save();
    }

    public function rewardReferral(LoyaltyReferral $referral, LoyaltyReferralProgram $program): void
    {
        if ($referral->status === 'rewarded') {
            return;
        }

        $referrer = $referral->referrer;
        $referee = $referral->referee;
        if (! $referrer) {
            return;
        }

        $idempotencyKey = 'referral:'.$referral->getKey();

        if ($program->reward_type === 'cashback') {
            if ($program->referrer_cashback > 0) {
                $this->ledgerService->creditCashback($referrer, (float) $program->referrer_cashback, $idempotencyKey.':referrer', ['referral_id' => $referral->getKey()]);
            }
            if ($referee && $program->referee_cashback > 0) {
                $this->ledgerService->creditCashback($referee, (float) $program->referee_cashback, $idempotencyKey.':referee', ['referral_id' => $referral->getKey()]);
            }
        } else {
            if ($program->referrer_points > 0) {
                $this->ledgerService->creditPoints($referrer, (int) $program->referrer_points, $idempotencyKey.':referrer', ['referral_id' => $referral->getKey()]);
            }
            if ($referee && $program->referee_points > 0) {
                $this->ledgerService->creditPoints($referee, (int) $program->referee_points, $idempotencyKey.':referee', ['referral_id' => $referral->getKey()]);
            }
        }

        $referral->status = 'rewarded';
        $referral->rewarded_at = now();
        $referral->save();

        $this->auditService->record('referral_rewarded', [
            'referral_id' => $referral->getKey(),
            'program_id' => $program->getKey(),
            'reward_type' => $program->reward_type,
        ], $referral);
    }

    public function processDueRewards(): int
    {
        $due = LoyaltyReferral::query()
            ->where('status', 'qualified')
            ->whereNotNull('reward_due_at')
            ->where('reward_due_at', '<=', now())
            ->get();

        $processed = 0;
        foreach ($due as $referral) {
            $program = $referral->program;
            if (! $program || $program->status !== 'active') {
                continue;
            }

            $this->rewardReferral($referral, $program);
            $processed += 1;
        }

        return $processed;
    }

    protected function generateCode(LoyaltyReferralProgram $program, LoyaltyCustomer $referrer): string
    {
        $prefix = $program->code_prefix ?: 'REF';
        $code = $prefix.'-'.Str::upper(Str::random(8));

        while (LoyaltyReferral::query()->where('tenant_id', $referrer->tenant_id)->where('referral_code', $code)->exists()) {
            $code = $prefix.'-'.Str::upper(Str::random(8));
        }

        return $code;
    }

    protected function isSelfReferral(LoyaltyCustomer $referrer, LoyaltyReferral $referral): bool
    {
        if (! (bool) config('filament-loyalty-club.referrals.fraud.block_self_referral', true)) {
            return false;
        }

        if ($referral->referee_phone && $referrer->phone && $referral->referee_phone === $referrer->phone) {
            return true;
        }

        if ($referral->referee_email && $referrer->email && $referral->referee_email === $referrer->email) {
            return true;
        }

        return false;
    }

    protected function exceedsReferralLimits(LoyaltyReferralProgram $program, LoyaltyReferral $referral): bool
    {
        if (! $program->max_per_referrer) {
            return false;
        }

        $query = LoyaltyReferral::query()
            ->where('tenant_id', $referral->tenant_id)
            ->where('referrer_customer_id', $referral->referrer_customer_id)
            ->whereIn('status', ['qualified', 'rewarded']);

        if ($program->period_days) {
            $query->where('created_at', '>=', now()->subDays((int) $program->period_days));
        }

        return $query->count() >= (int) $program->max_per_referrer;
    }

    protected function flagReferral(LoyaltyReferral $referral, string $type, string $reason): void
    {
        $referral->status = 'flagged';
        $referral->fraud_reason = $reason;
        $referral->flagged_at = now();
        $referral->save();

        LoyaltyFraudSignal::query()->create([
            'tenant_id' => $referral->tenant_id,
            'customer_id' => $referral->referrer_customer_id,
            'type' => $type,
            'severity' => 'medium',
            'status' => 'open',
            'subject_type' => LoyaltyReferral::class,
            'subject_id' => $referral->getKey(),
            'score' => 60,
            'detected_at' => now(),
            'meta' => [
                'reason' => $reason,
                'referral_code' => $referral->referral_code,
            ],
        ]);

        $this->auditService->record('referral_flagged', [
            'referral_id' => $referral->getKey(),
            'reason' => $reason,
            'signal_type' => $type,
        ], $referral);
    }
}

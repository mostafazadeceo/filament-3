<?php

namespace Haida\FilamentLoyaltyClub\Services;

use Haida\FilamentLoyaltyClub\Models\LoyaltyCoupon;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyDonationPledge;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReward;
use Haida\FilamentLoyaltyClub\Models\LoyaltyRewardRedemption;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;
use RuntimeException;

class LoyaltyRewardService
{
    public function __construct(
        protected DatabaseManager $db,
        protected LoyaltyLedgerService $ledgerService,
        protected LoyaltyAuditService $auditService,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function redeemReward(LoyaltyCustomer $customer, LoyaltyReward $reward, array $payload = []): LoyaltyRewardRedemption
    {
        return $this->db->transaction(function () use ($customer, $reward, $payload) {
            $lockedReward = LoyaltyReward::query()
                ->where('tenant_id', $customer->tenant_id)
                ->lockForUpdate()
                ->find($reward->getKey());

            if (! $lockedReward) {
                throw new RuntimeException('پاداش یافت نشد.');
            }

            if ($lockedReward->status !== 'active') {
                throw new RuntimeException('پاداش غیرفعال است.');
            }
            if ($lockedReward->valid_from && now()->lt($lockedReward->valid_from)) {
                throw new RuntimeException('زمان پاداش هنوز شروع نشده است.');
            }
            if ($lockedReward->valid_until && now()->gt($lockedReward->valid_until)) {
                throw new RuntimeException('زمان پاداش پایان یافته است.');
            }
            if ($lockedReward->inventory !== null && $lockedReward->inventory <= 0) {
                throw new RuntimeException('موجودی پاداش کافی نیست.');
            }

            $idempotencyKey = (string) ($payload['idempotency_key'] ?? 'reward:'.$lockedReward->getKey().':'.$customer->getKey());

            $existing = LoyaltyRewardRedemption::query()
                ->where('tenant_id', $customer->tenant_id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();
            if ($existing) {
                return $existing;
            }

            if ($lockedReward->type === 'donation' && ! (bool) config('filament-loyalty-club.features.charity_redemption', true)) {
                throw new RuntimeException('امکان بازخرید خیریه غیرفعال است.');
            }

            if ($lockedReward->points_cost > 0) {
                $this->ledgerService->debitPoints($customer, (int) $lockedReward->points_cost, $idempotencyKey.':points', ['reward_id' => $lockedReward->getKey()]);
            }
            if ($lockedReward->cashback_cost > 0) {
                $this->ledgerService->debitCashback($customer, (float) $lockedReward->cashback_cost, $idempotencyKey.':cashback', ['reward_id' => $lockedReward->getKey()]);
            }

            $redemption = LoyaltyRewardRedemption::query()->create([
                'tenant_id' => $customer->tenant_id,
                'reward_id' => $lockedReward->getKey(),
                'customer_id' => $customer->getKey(),
                'points_spent' => (int) $lockedReward->points_cost,
                'cashback_spent' => (float) $lockedReward->cashback_cost,
                'idempotency_key' => $idempotencyKey,
                'status' => 'redeemed',
                'meta' => $payload,
                'redeemed_at' => now(),
            ]);

            if ($lockedReward->inventory !== null) {
                $lockedReward->inventory = max(0, $lockedReward->inventory - 1);
                $lockedReward->save();
            }

            if (in_array($lockedReward->type, ['discount', 'gift_card', 'shipping'], true)) {
                $coupon = $this->issueCouponForReward($customer, $lockedReward, $payload);
                $redemption->reference_type = LoyaltyCoupon::class;
                $redemption->reference_id = $coupon->getKey();
                $redemption->save();
            }

            if ($lockedReward->type === 'donation') {
                $pledge = LoyaltyDonationPledge::query()->create([
                    'tenant_id' => $customer->tenant_id,
                    'customer_id' => $customer->getKey(),
                    'reward_id' => $lockedReward->getKey(),
                    'redemption_id' => $redemption->getKey(),
                    'points_spent' => (int) $lockedReward->points_cost,
                    'cashback_spent' => (float) $lockedReward->cashback_cost,
                    'charity_name' => $payload['charity_name'] ?? null,
                    'charity_reference' => $payload['charity_reference'] ?? null,
                    'status' => 'pledged',
                    'pledged_at' => now(),
                    'meta' => $payload,
                ]);

                $redemption->reference_type = LoyaltyDonationPledge::class;
                $redemption->reference_id = $pledge->getKey();
                $redemption->save();

                $this->auditService->record('donation_pledged', [
                    'pledge_id' => $pledge->getKey(),
                    'reward_id' => $lockedReward->getKey(),
                    'points_spent' => (int) $lockedReward->points_cost,
                    'cashback_spent' => (float) $lockedReward->cashback_cost,
                ], $pledge);
            }

            $this->auditService->record('reward_redeemed', [
                'reward_id' => $lockedReward->getKey(),
                'points_spent' => (int) $lockedReward->points_cost,
                'cashback_spent' => (float) $lockedReward->cashback_cost,
                'type' => $lockedReward->type,
            ], $redemption);

            return $redemption;
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function issueCouponForReward(LoyaltyCustomer $customer, LoyaltyReward $reward, array $payload = []): LoyaltyCoupon
    {
        $code = $this->generateCouponCode($customer);

        return LoyaltyCoupon::query()->create([
            'tenant_id' => $customer->tenant_id,
            'reward_id' => $reward->getKey(),
            'issued_to_customer_id' => $customer->getKey(),
            'code' => $code,
            'type' => $reward->type,
            'discount_type' => $payload['discount_type'] ?? null,
            'discount_value' => $payload['discount_value'] ?? null,
            'currency' => $payload['currency'] ?? null,
            'max_uses' => 1,
            'max_uses_per_customer' => 1,
            'stackable' => false,
            'status' => 'active',
            'source' => 'reward',
            'valid_from' => $reward->valid_from,
            'valid_until' => $reward->valid_until,
            'constraints' => $reward->constraints,
            'meta' => $payload,
        ]);
    }

    protected function generateCouponCode(LoyaltyCustomer $customer): string
    {
        $code = strtoupper('LC-'.Str::random(10));

        while (LoyaltyCoupon::query()->where('tenant_id', $customer->tenant_id)->where('code', $code)->exists()) {
            $code = strtoupper('LC-'.Str::random(10));
        }

        return $code;
    }
}

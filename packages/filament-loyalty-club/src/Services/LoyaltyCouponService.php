<?php

namespace Haida\FilamentLoyaltyClub\Services;

use Haida\FilamentLoyaltyClub\Models\LoyaltyCoupon;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCouponRedemption;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Illuminate\Database\DatabaseManager;
use RuntimeException;

class LoyaltyCouponService
{
    public function __construct(
        protected DatabaseManager $db,
        protected LoyaltyAuditService $auditService,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public function validateCoupon(LoyaltyCustomer $customer, string $code, array $context = []): LoyaltyCoupon
    {
        $coupon = LoyaltyCoupon::query()
            ->where('tenant_id', $customer->tenant_id)
            ->where('code', $code)
            ->first();

        $this->assertCouponValid($coupon, $customer, $context);

        return $coupon;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function redeemCoupon(LoyaltyCustomer $customer, string $code, array $context = []): LoyaltyCouponRedemption
    {
        return $this->db->transaction(function () use ($customer, $code, $context) {
            $coupon = LoyaltyCoupon::query()
                ->where('tenant_id', $customer->tenant_id)
                ->where('code', $code)
                ->lockForUpdate()
                ->first();

            $this->assertCouponValid($coupon, $customer, $context);

            $redemption = LoyaltyCouponRedemption::query()->create([
                'tenant_id' => $customer->tenant_id,
                'coupon_id' => $coupon->getKey(),
                'customer_id' => $customer->getKey(),
                'order_reference' => $context['order_reference'] ?? null,
                'status' => 'redeemed',
                'meta' => $context,
                'redeemed_at' => now(),
            ]);

            $coupon->used_count += 1;
            $coupon->save();

            $this->auditService->record('coupon_redeemed', [
                'coupon_id' => $coupon->getKey(),
                'code' => $coupon->code,
                'order_reference' => $context['order_reference'] ?? null,
            ], $redemption);

            return $redemption;
        });
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function assertCouponValid(?LoyaltyCoupon $coupon, LoyaltyCustomer $customer, array $context = []): void
    {
        if (! $coupon || $coupon->status !== 'active') {
            throw new RuntimeException('کوپن معتبر نیست.');
        }

        if ($coupon->valid_from && now()->lt($coupon->valid_from)) {
            throw new RuntimeException('کوپن هنوز فعال نشده است.');
        }
        if ($coupon->valid_until && now()->gt($coupon->valid_until)) {
            throw new RuntimeException('اعتبار کوپن تمام شده است.');
        }

        if ($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses) {
            throw new RuntimeException('سقف استفاده از کوپن پر شده است.');
        }

        if ($coupon->max_uses_per_customer !== null) {
            $count = LoyaltyCouponRedemption::query()
                ->where('tenant_id', $customer->tenant_id)
                ->where('coupon_id', $coupon->getKey())
                ->where('customer_id', $customer->getKey())
                ->count();
            if ($count >= $coupon->max_uses_per_customer) {
                throw new RuntimeException('سقف استفاده برای مشتری پر شده است.');
            }
        }

        $constraints = (array) ($coupon->constraints ?? []);
        if (isset($constraints['min_amount']) && isset($context['amount'])) {
            if ((float) $context['amount'] < (float) $constraints['min_amount']) {
                throw new RuntimeException('حداقل مبلغ برای استفاده رعایت نشده است.');
            }
        }

        if ($coupon->issued_to_customer_id && $coupon->issued_to_customer_id !== $customer->getKey()) {
            throw new RuntimeException('کوپن برای این مشتری صادر نشده است.');
        }
    }
}

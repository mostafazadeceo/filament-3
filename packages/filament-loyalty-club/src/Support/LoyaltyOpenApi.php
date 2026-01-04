<?php

namespace Haida\FilamentLoyaltyClub\Support;

class LoyaltyOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Loyalty Club API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/loyalty/customers' => [
                    'get' => ['summary' => 'List customers'],
                    'post' => ['summary' => 'Create customer'],
                ],
                '/api/v1/loyalty/customers/{customer}' => [
                    'get' => ['summary' => 'Show customer'],
                    'put' => ['summary' => 'Update customer'],
                ],
                '/api/v1/loyalty/customers/{customer}/balances' => [
                    'get' => ['summary' => 'Customer balances'],
                ],
                '/api/v1/loyalty/events' => [
                    'post' => ['summary' => 'Ingest loyalty event'],
                ],
                '/api/v1/loyalty/rewards' => [
                    'get' => ['summary' => 'List rewards'],
                ],
                '/api/v1/loyalty/rewards/{reward}/redeem' => [
                    'post' => ['summary' => 'Redeem reward'],
                ],
                '/api/v1/loyalty/coupons/validate' => [
                    'post' => ['summary' => 'Validate coupon'],
                ],
                '/api/v1/loyalty/coupons/redeem' => [
                    'post' => ['summary' => 'Redeem coupon'],
                ],
                '/api/v1/loyalty/referrals' => [
                    'post' => ['summary' => 'Create referral'],
                ],
                '/api/v1/loyalty/referrals/{referral}' => [
                    'get' => ['summary' => 'Show referral'],
                ],
                '/api/v1/loyalty/missions' => [
                    'get' => ['summary' => 'List missions'],
                ],
                '/api/v1/loyalty/missions/{mission}/progress' => [
                    'get' => ['summary' => 'Mission progress'],
                ],
                '/api/v1/loyalty/campaigns/offers' => [
                    'get' => ['summary' => 'Personalized offers'],
                ],
                '/api/v1/loyalty/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}

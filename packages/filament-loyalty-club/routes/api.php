<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1\CampaignController;
use Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1\CouponController;
use Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1\CustomerController;
use Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1\EventController;
use Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1\MissionController;
use Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1\OpenApiController;
use Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1\ReferralController;
use Haida\FilamentLoyaltyClub\Http\Controllers\Api\V1\RewardController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/loyalty')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-loyalty-club.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::get('customers', [CustomerController::class, 'index'])
            ->middleware('filamat-iam.scope:loyalty.customer.view');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])
            ->middleware('filamat-iam.scope:loyalty.customer.view');
        Route::post('customers', [CustomerController::class, 'store'])
            ->middleware('filamat-iam.scope:loyalty.customer.manage');
        Route::put('customers/{customer}', [CustomerController::class, 'update'])
            ->middleware('filamat-iam.scope:loyalty.customer.manage');
        Route::get('customers/{customer}/balances', [CustomerController::class, 'balances'])
            ->middleware('filamat-iam.scope:loyalty.customer.view');

        Route::post('events', [EventController::class, 'store'])
            ->middleware('filamat-iam.scope:loyalty.event.ingest');

        Route::get('rewards', [RewardController::class, 'index'])
            ->middleware('filamat-iam.scope:loyalty.reward.view');
        Route::post('rewards/{reward}/redeem', [RewardController::class, 'redeem'])
            ->middleware('filamat-iam.scope:loyalty.reward.redeem');

        Route::post('coupons/validate', [CouponController::class, 'validateCoupon'])
            ->middleware('filamat-iam.scope:loyalty.coupon.view');
        Route::post('coupons/redeem', [CouponController::class, 'redeem'])
            ->middleware('filamat-iam.scope:loyalty.coupon.redeem');

        Route::post('referrals', [ReferralController::class, 'store'])
            ->middleware('filamat-iam.scope:loyalty.referral.manage');
        Route::get('referrals/{referral}', [ReferralController::class, 'show'])
            ->middleware('filamat-iam.scope:loyalty.referral.view');

        Route::get('missions', [MissionController::class, 'index'])
            ->middleware('filamat-iam.scope:loyalty.mission.view');
        Route::get('missions/{mission}/progress', [MissionController::class, 'progress'])
            ->middleware('filamat-iam.scope:loyalty.mission.view');

        Route::get('campaigns/offers', [CampaignController::class, 'offers'])
            ->middleware('filamat-iam.scope:loyalty.campaign.view');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:loyalty.view');
    });

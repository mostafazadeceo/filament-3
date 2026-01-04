<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\TenancyDomains\Http\Controllers\Api\V1\DomainController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/tenancy-domains')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('tenancy-domains.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::get('domains', [DomainController::class, 'index'])
            ->middleware('filamat-iam.scope:site.domain.view');
        Route::post('domains', [DomainController::class, 'store'])
            ->middleware('filamat-iam.scope:site.domain.manage');
        Route::get('domains/{domain}', [DomainController::class, 'show'])
            ->middleware('filamat-iam.scope:site.domain.view');
        Route::patch('domains/{domain}', [DomainController::class, 'update'])
            ->middleware('filamat-iam.scope:site.domain.manage');
        Route::delete('domains/{domain}', [DomainController::class, 'destroy'])
            ->middleware('filamat-iam.scope:site.domain.manage');

        Route::post('domains/{domain}/request-verification', [DomainController::class, 'requestVerification'])
            ->middleware('filamat-iam.scope:site.domain.manage')
            ->middleware('throttle:'.config('tenancy-domains.api.verify_rate_limit', '20,1'));
        Route::post('domains/{domain}/verify', [DomainController::class, 'verify'])
            ->middleware('filamat-iam.scope:site.domain.manage')
            ->middleware('throttle:'.config('tenancy-domains.api.verify_rate_limit', '20,1'));
        Route::post('domains/{domain}/request-tls', [DomainController::class, 'requestTls'])
            ->middleware('filamat-iam.scope:site.domain.manage')
            ->middleware('throttle:'.config('tenancy-domains.api.verify_rate_limit', '20,1'));
    });

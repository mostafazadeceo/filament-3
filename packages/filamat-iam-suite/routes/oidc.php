<?php

use Filamat\IamSuite\Http\Controllers\Oidc\OidcController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    Route::get('/.well-known/openid-configuration', [OidcController::class, 'configuration']);
    Route::get(config('filamat-iam.sso.oidc.jwks_path', '/oidc/jwks.json'), [OidcController::class, 'jwks']);
    Route::post(config('filamat-iam.sso.oidc.token_path', '/oidc/token'), [OidcController::class, 'token']);
    Route::match(['GET', 'POST'], config('filamat-iam.sso.oidc.userinfo_path', '/oidc/userinfo'), [OidcController::class, 'userinfo']);
});

Route::middleware(['web'])->group(function () {
    Route::match(['GET', 'POST'], config('filamat-iam.sso.oidc.authorize_path', '/oidc/authorize'), [OidcController::class, 'authorize']);
});

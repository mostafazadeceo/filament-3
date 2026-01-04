<?php

use Filamat\IamSuite\Http\Controllers\Api\V1\GroupController;
use Filamat\IamSuite\Http\Controllers\Api\V1\ImpersonationApiController;
use Filamat\IamSuite\Http\Controllers\Api\V1\InvitationController;
use Filamat\IamSuite\Http\Controllers\Api\V1\MfaController;
use Filamat\IamSuite\Http\Controllers\Api\V1\N8nCallbackController;
use Filamat\IamSuite\Http\Controllers\Api\V1\NotificationController;
use Filamat\IamSuite\Http\Controllers\Api\V1\PermissionController;
use Filamat\IamSuite\Http\Controllers\Api\V1\PrivilegeActivationController;
use Filamat\IamSuite\Http\Controllers\Api\V1\PrivilegeEligibilityController;
use Filamat\IamSuite\Http\Controllers\Api\V1\PrivilegeRequestController;
use Filamat\IamSuite\Http\Controllers\Api\V1\ProtectedActionController;
use Filamat\IamSuite\Http\Controllers\Api\V1\RoleController;
use Filamat\IamSuite\Http\Controllers\Api\V1\ScimController;
use Filamat\IamSuite\Http\Controllers\Api\V1\SessionController;
use Filamat\IamSuite\Http\Controllers\Api\V1\SsoController;
use Filamat\IamSuite\Http\Controllers\Api\V1\SubscriptionController;
use Filamat\IamSuite\Http\Controllers\Api\V1\SubscriptionPlanController;
use Filamat\IamSuite\Http\Controllers\Api\V1\TenantController;
use Filamat\IamSuite\Http\Controllers\Api\V1\UserController;
use Filamat\IamSuite\Http\Controllers\Api\V1\WalletController;
use Filamat\IamSuite\Http\Controllers\Api\V1\WalletHoldController;
use Filamat\IamSuite\Http\Controllers\Api\V1\WalletTransactionController;
use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')
    ->middleware(['api', ApiKeyAuth::class, ApiAuth::class, ResolveTenant::class, 'throttle:'.config('filamat-iam.api.rate_limit', '60,1')])
    ->group(function () {
        Route::apiResource('tenants', TenantController::class)->middleware('filamat-iam.scope:iam');
        Route::apiResource('users', UserController::class)->middleware('filamat-iam.scope:iam');
        Route::apiResource('roles', RoleController::class)->middleware('filamat-iam.scope:iam');
        Route::apiResource('permissions', PermissionController::class)->middleware('filamat-iam.scope:iam');
        Route::apiResource('groups', GroupController::class)->middleware('filamat-iam.scope:iam');
        Route::apiResource('wallets', WalletController::class)->middleware('filamat-iam.scope:wallet');
        Route::apiResource('transactions', WalletTransactionController::class)->only(['index', 'show'])->middleware('filamat-iam.scope:wallet');
        Route::apiResource('wallet-holds', WalletHoldController::class)->only(['index', 'show'])->middleware('filamat-iam.scope:wallet');
        Route::apiResource('plans', SubscriptionPlanController::class)->middleware('filamat-iam.scope:subscription');
        Route::apiResource('subscriptions', SubscriptionController::class)->middleware('filamat-iam.scope:subscription');

        Route::post('notifications/send', [NotificationController::class, 'send'])->middleware('filamat-iam.scope:notification.send');

        Route::post('wallets/{wallet}/credit', [WalletController::class, 'credit'])->middleware('filamat-iam.scope:wallet');
        Route::post('wallets/{wallet}/debit', [WalletController::class, 'debit'])->middleware('filamat-iam.scope:wallet');
        Route::post('wallets/{wallet}/holds', [WalletHoldController::class, 'store'])->middleware('filamat-iam.scope:wallet');
        Route::post('wallets/transfer', [WalletController::class, 'transfer'])->middleware('filamat-iam.scope:wallet');
        Route::post('wallet-holds/{hold}/capture', [WalletHoldController::class, 'capture'])->middleware('filamat-iam.scope:wallet');
        Route::post('wallet-holds/{hold}/release', [WalletHoldController::class, 'release'])->middleware('filamat-iam.scope:wallet');
    });

Route::prefix('api/v1/iam')
    ->middleware(['api', ApiKeyAuth::class, ApiAuth::class, ResolveTenant::class, 'throttle:'.config('filamat-iam.api.rate_limit', '60,1')])
    ->group(function () {
        Route::get('invitations', [InvitationController::class, 'index'])->middleware('filamat-iam.scope:user.view');
        Route::get('invitations/{invitation}', [InvitationController::class, 'show'])->middleware('filamat-iam.scope:user.view');
        Route::post('invitations', [InvitationController::class, 'store'])->middleware('filamat-iam.scope:user.invite');
        Route::post('invitations/{invitation}/accept', [InvitationController::class, 'accept'])->middleware('filamat-iam.scope:user.invite');
        Route::post('invitations/{invitation}/revoke', [InvitationController::class, 'revoke'])->middleware('filamat-iam.scope:user.invite');

        Route::post('n8n/callback', N8nCallbackController::class)->middleware('filamat-iam.scope:automation.manage');

        Route::apiResource('privilege-eligibilities', PrivilegeEligibilityController::class)->middleware('filamat-iam.scope:pam');
        Route::apiResource('privilege-requests', PrivilegeRequestController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:pam');
        Route::post('privilege-requests', [PrivilegeRequestController::class, 'store'])->middleware('filamat-iam.scope:pam.request');
        Route::post('privilege-requests/{requestModel}/approve', [PrivilegeRequestController::class, 'approve'])->middleware('filamat-iam.scope:pam.approve');
        Route::post('privilege-requests/{requestModel}/deny', [PrivilegeRequestController::class, 'deny'])->middleware('filamat-iam.scope:pam.approve');

        Route::apiResource('privilege-activations', PrivilegeActivationController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:pam');
        Route::post('privilege-activations', [PrivilegeActivationController::class, 'store'])->middleware('filamat-iam.scope:pam.activate');
        Route::post('privilege-activations/{activation}/revoke', [PrivilegeActivationController::class, 'revoke'])->middleware('filamat-iam.scope:pam.revoke');

        Route::post('impersonations/start', [ImpersonationApiController::class, 'start'])->middleware('filamat-iam.scope:iam.impersonate');
        Route::post('impersonations/stop', [ImpersonationApiController::class, 'stop'])->middleware('filamat-iam.scope:iam.impersonate');

        Route::get('sessions', [SessionController::class, 'index'])->middleware('filamat-iam.scope:session');
        Route::post('sessions/{session}/revoke', [SessionController::class, 'revoke'])->middleware('filamat-iam.scope:session.revoke');

        Route::post('protected-actions/verify', [ProtectedActionController::class, 'verify'])->middleware('filamat-iam.scope:iam');

        Route::post('mfa/totp/start', [MfaController::class, 'startTotp'])->middleware('filamat-iam.scope:mfa.manage');
        Route::post('mfa/totp/confirm', [MfaController::class, 'confirmTotp'])->middleware('filamat-iam.scope:mfa.manage');
        Route::post('mfa/totp/reset', [MfaController::class, 'resetTotp'])->middleware('filamat-iam.scope:mfa.reset');

        Route::get('scim/Users', [ScimController::class, 'users'])->middleware('filamat-iam.scope:scim.view');
        Route::post('scim/Users', [ScimController::class, 'createUser'])->middleware('filamat-iam.scope:scim.manage');
        Route::patch('scim/Users/{id}', [ScimController::class, 'updateUser'])->middleware('filamat-iam.scope:scim.manage');
        Route::delete('scim/Users/{id}', [ScimController::class, 'deleteUser'])->middleware('filamat-iam.scope:scim.manage');
        Route::get('scim/Groups', [ScimController::class, 'groups'])->middleware('filamat-iam.scope:scim.view');

        Route::get('sso/providers', [SsoController::class, 'providers'])->middleware('filamat-iam.scope:sso.view');
        Route::post('sso/oidc/callback', [SsoController::class, 'oidcCallback'])->middleware('filamat-iam.scope:sso.manage');
    });

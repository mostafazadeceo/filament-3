<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCryptoCore\Models\CryptoFeePolicy;
use Haida\FilamentCryptoCore\Services\FeePolicyEngine;
use Haida\FilamentCryptoGateway\Services\PlanService;
use Illuminate\Http\JsonResponse;

class PolicyController extends ApiController
{
    public function show(FeePolicyEngine $engine, PlanService $plans): JsonResponse
    {
        $this->authorize('viewAny', CryptoFeePolicy::class);

        $tenantId = TenantContext::getTenantId();
        $policy = $engine->policyForTenant($tenantId);

        return response()->json([
            'plan' => $policy['plan'] ?? 'free',
            'fees' => $policy['fees'] ?? [],
            'features' => [
                'providers' => $plans->allowsFeature($tenantId, 'crypto.providers'),
                'payouts' => $plans->allowsFeature($tenantId, 'crypto.payouts'),
                'payout_approval' => (bool) config('filament-crypto-gateway.payouts.require_approval', true),
                'payout_whitelist' => (bool) config('filament-crypto-gateway.payouts.whitelist.enabled', true),
                'webhook_replay' => $plans->allowsFeature($tenantId, 'crypto.webhook_replay'),
                'ai_auditor' => $plans->allowsFeature($tenantId, 'crypto.ai_auditor'),
                'nodes' => $plans->allowsFeature($tenantId, 'crypto.nodes'),
            ],
        ]);
    }
}

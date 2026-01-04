<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCryptoGateway\Http\Requests\PayoutApprovalRequest;
use Haida\FilamentCryptoGateway\Http\Requests\StorePayoutRequest;
use Haida\FilamentCryptoGateway\Http\Resources\CryptoPayoutResource;
use Haida\FilamentCryptoGateway\Models\CryptoPayout;
use Haida\FilamentCryptoGateway\Services\PayoutService;
use Illuminate\Support\Facades\Auth;

class PayoutController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(CryptoPayout::class, 'payout');
    }

    public function store(StorePayoutRequest $request, PayoutService $service): CryptoPayoutResource
    {
        $payload = $request->validated();
        $payload['tenant_id'] = (int) TenantContext::getTenantId();

        $payout = $service->create($payload);

        return new CryptoPayoutResource($payout);
    }

    public function show(CryptoPayout $payout): CryptoPayoutResource
    {
        return new CryptoPayoutResource($payout);
    }

    public function approve(PayoutApprovalRequest $request, CryptoPayout $payout, PayoutService $service): CryptoPayoutResource
    {
        $this->authorize('approve', $payout);

        $approved = $service->approve($payout, Auth::id(), $request->validated()['note'] ?? null);

        return new CryptoPayoutResource($approved);
    }

    public function reject(PayoutApprovalRequest $request, CryptoPayout $payout, PayoutService $service): CryptoPayoutResource
    {
        $this->authorize('approve', $payout);

        $rejected = $service->reject($payout, Auth::id(), $request->validated()['note'] ?? null);

        return new CryptoPayoutResource($rejected);
    }
}

<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Models\Wallet;
use Filamat\IamSuite\Models\WalletHold;
use Filamat\IamSuite\Services\WalletService;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WalletHoldController extends BaseController
{
    protected function modelClass(): string
    {
        return WalletHold::class;
    }

    protected function validationRules(string $action): array
    {
        return [
            'wallet_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric'],
            'status' => ['nullable', 'string'],
        ];
    }

    public function store(Request $request, ?int $wallet = null): Response
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['nullable', 'string', 'max:255'],
            'idempotency_key' => ['nullable', 'string', 'max:255'],
            'meta' => ['nullable', 'array'],
        ]);

        $walletId = $wallet ?? (int) $request->input('wallet_id');
        if (! $walletId) {
            abort(422, 'شناسه کیف پول اجباری است.');
        }

        $walletModel = Wallet::query()->findOrFail($walletId);
        $this->assertTenantWallet($walletModel);

        $hold = app(WalletService::class)->hold(
            $walletModel,
            (float) $data['amount'],
            (string) ($data['reason'] ?? ''),
            $data['meta'] ?? [],
            $data['idempotency_key'] ?? null
        );

        return response(['data' => $hold], 201);
    }

    public function capture(Request $request, int $hold): Response
    {
        $data = $request->validate([
            'idempotency_key' => ['required', 'string', 'max:255'],
            'meta' => ['nullable', 'array'],
        ]);

        $record = WalletHold::query()->findOrFail($hold);
        $this->assertTenantHold($record);

        $transaction = app(WalletService::class)->captureHold($record, (string) $data['idempotency_key'], $data['meta'] ?? []);

        return response(['data' => $transaction], 200);
    }

    public function release(Request $request, int $hold): Response
    {
        $data = $request->validate([
            'idempotency_key' => ['required', 'string', 'max:255'],
            'meta' => ['nullable', 'array'],
        ]);

        $record = WalletHold::query()->findOrFail($hold);
        $this->assertTenantHold($record);

        $transaction = app(WalletService::class)->releaseHold($record, (string) $data['idempotency_key'], $data['meta'] ?? []);

        return response(['data' => $transaction], 200);
    }

    protected function applyTenantScope(Builder $query): Builder
    {
        if (TenantContext::shouldBypass()) {
            return $query;
        }

        $tenantId = TenantContext::getTenantId();
        if (! $tenantId) {
            return $query;
        }

        return $query->whereHas('wallet', function (Builder $builder) use ($tenantId) {
            $builder->where('tenant_id', $tenantId);
        });
    }

    protected function assertTenantWallet(Wallet $wallet): void
    {
        if (TenantContext::shouldBypass()) {
            return;
        }

        $tenantId = TenantContext::getTenantId();
        if ($tenantId && (int) $wallet->tenant_id !== $tenantId) {
            abort(403, 'این کیف پول در این فضا مجاز نیست.');
        }
    }

    protected function assertTenantHold(WalletHold $hold): void
    {
        $wallet = $hold->wallet;
        if (! $wallet) {
            abort(404, 'کیف پول یافت نشد.');
        }

        $this->assertTenantWallet($wallet);
    }
}

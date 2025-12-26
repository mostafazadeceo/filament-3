<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Models\Wallet;
use Filamat\IamSuite\Services\WalletService;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class WalletController extends BaseController
{
    protected function modelClass(): string
    {
        return Wallet::class;
    }

    protected function validationRules(string $action): array
    {
        return [
            'tenant_id' => [Rule::requiredIf(TenantContext::shouldBypass()), 'integer'],
            'user_id' => ['required', 'integer'],
            'currency' => ['required', 'string', 'max:10'],
            'status' => ['nullable', 'string'],
        ];
    }

    public function credit(Request $request, int $wallet): Response
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'idempotency_key' => ['required', 'string', 'max:255'],
            'meta' => ['nullable', 'array'],
        ]);

        $record = Wallet::query()->findOrFail($wallet);
        $this->assertTenantWallet($record);

        $transaction = app(WalletService::class)->credit($record, (float) $data['amount'], (string) $data['idempotency_key'], $data['meta'] ?? []);

        return response(['data' => $transaction], 201);
    }

    public function debit(Request $request, int $wallet): Response
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'idempotency_key' => ['required', 'string', 'max:255'],
            'meta' => ['nullable', 'array'],
        ]);

        $record = Wallet::query()->findOrFail($wallet);
        $this->assertTenantWallet($record);

        $transaction = app(WalletService::class)->debit($record, (float) $data['amount'], (string) $data['idempotency_key'], $data['meta'] ?? []);

        return response(['data' => $transaction], 201);
    }

    public function transfer(Request $request): Response
    {
        $data = $request->validate([
            'from_wallet_id' => ['required', 'integer'],
            'to_wallet_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'idempotency_key' => ['required', 'string', 'max:255'],
            'meta' => ['nullable', 'array'],
        ]);

        $from = Wallet::query()->findOrFail($data['from_wallet_id']);
        $to = Wallet::query()->findOrFail($data['to_wallet_id']);
        $this->assertTenantWallet($from);
        $this->assertTenantWallet($to);

        $transactions = app(WalletService::class)->transfer($from, $to, (float) $data['amount'], (string) $data['idempotency_key'], $data['meta'] ?? []);

        return response(['data' => $transactions], 201);
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
}

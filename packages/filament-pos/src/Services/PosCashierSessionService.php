<?php

namespace Haida\FilamentPos\Services;

use Haida\FilamentPos\Models\PosCashierSession;
use Haida\FilamentPos\Models\PosCashMovement;
use Haida\FilamentPos\Models\PosRegister;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\DatabaseManager;

class PosCashierSessionService
{
    public function __construct(protected DatabaseManager $db) {}

    public function openSession(PosRegister $register, float $openingFloat = 0, ?int $deviceId = null, ?Authenticatable $actor = null): PosCashierSession
    {
        return $this->db->transaction(function () use ($register, $openingFloat, $deviceId, $actor): PosCashierSession {
            $session = PosCashierSession::query()->create([
                'tenant_id' => $register->tenant_id,
                'store_id' => $register->store_id,
                'register_id' => $register->getKey(),
                'device_id' => $deviceId,
                'opened_by_user_id' => $actor?->getAuthIdentifier(),
                'status' => 'open',
                'opened_at' => now(),
                'opening_float' => $openingFloat,
                'expected_cash' => $openingFloat,
            ]);

            if ($openingFloat > 0) {
                PosCashMovement::query()->create([
                    'tenant_id' => $register->tenant_id,
                    'session_id' => $session->getKey(),
                    'type' => 'open_float',
                    'amount' => $openingFloat,
                    'recorded_at' => now(),
                    'created_by_user_id' => $actor?->getAuthIdentifier(),
                ]);
            }

            $register->update([
                'last_opened_at' => now(),
            ]);

            return $session->refresh();
        });
    }

    public function closeSession(PosCashierSession $session, float $closingCash, ?Authenticatable $actor = null): PosCashierSession
    {
        return $this->db->transaction(function () use ($session, $closingCash, $actor): PosCashierSession {
            $expected = $this->calculateExpectedCash($session);
            $variance = $closingCash - $expected;

            $session->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closing_cash' => $closingCash,
                'expected_cash' => $expected,
                'variance' => $variance,
                'closed_by_user_id' => $actor?->getAuthIdentifier(),
            ]);

            $session->register()->update([
                'last_closed_at' => now(),
            ]);

            PosCashMovement::query()->create([
                'tenant_id' => $session->tenant_id,
                'session_id' => $session->getKey(),
                'type' => 'close_reconcile',
                'amount' => $closingCash,
                'recorded_at' => now(),
                'created_by_user_id' => $actor?->getAuthIdentifier(),
            ]);

            return $session->refresh();
        });
    }

    public function recordMovement(PosCashierSession $session, string $type, float $amount, ?string $reason = null, ?Authenticatable $actor = null): PosCashMovement
    {
        return $this->db->transaction(function () use ($session, $type, $amount, $reason, $actor): PosCashMovement {
            $movement = PosCashMovement::query()->create([
                'tenant_id' => $session->tenant_id,
                'session_id' => $session->getKey(),
                'type' => $type,
                'amount' => $amount,
                'reason' => $reason,
                'recorded_at' => now(),
                'created_by_user_id' => $actor?->getAuthIdentifier(),
            ]);

            $session->update([
                'expected_cash' => $this->calculateExpectedCash($session->refresh()),
            ]);

            return $movement;
        });
    }

    protected function calculateExpectedCash(PosCashierSession $session): float
    {
        $opening = (float) $session->opening_float;

        $movements = PosCashMovement::query()
            ->where('session_id', $session->getKey())
            ->get();

        $payIn = $movements->where('type', 'pay_in')->sum('amount');
        $payOut = $movements->where('type', 'pay_out')->sum('amount');
        $drops = $movements->where('type', 'cash_drop')->sum('amount');

        return (float) ($opening + $payIn - $payOut - $drops);
    }
}

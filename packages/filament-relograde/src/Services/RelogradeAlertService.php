<?php

namespace Haida\FilamentRelograde\Services;

use Haida\FilamentRelograde\Models\RelogradeAlert;
use Haida\FilamentRelograde\Models\RelogradeConnection;

class RelogradeAlertService
{
    public function checkLowBalances(?RelogradeConnection $connection = null): int
    {
        $thresholds = config('relograde.low_balance_thresholds', []);
        if (! is_array($thresholds) || $thresholds === []) {
            return 0;
        }

        $connections = $connection
            ? collect([$connection])
            : RelogradeConnection::query()->get();

        $alerts = 0;

        foreach ($connections as $conn) {
            $accounts = $conn->accounts()->get();

            foreach ($accounts as $account) {
                $threshold = $thresholds[$account->currency] ?? null;
                if ($threshold === null) {
                    continue;
                }

                $existing = RelogradeAlert::query()
                    ->where('connection_id', $conn->getKey())
                    ->where('type', 'low_balance')
                    ->where('currency', $account->currency)
                    ->whereNull('resolved_at')
                    ->first();

                if ($account->total_amount < $threshold) {
                    $severity = $account->total_amount < ($threshold * 0.5) ? 'critical' : 'warning';
                    $message = "موجودی {$account->currency} برابر {$account->total_amount} است و پایین‌تر از آستانه {$threshold} قرار دارد.";

                    if ($existing) {
                        $existing->update([
                            'severity' => $severity,
                            'current_amount' => $account->total_amount,
                            'threshold' => $threshold,
                            'message' => $message,
                        ]);
                    } else {
                        RelogradeAlert::create([
                            'connection_id' => $conn->getKey(),
                            'type' => 'low_balance',
                            'severity' => $severity,
                            'currency' => $account->currency,
                            'current_amount' => $account->total_amount,
                            'threshold' => $threshold,
                            'message' => $message,
                        ]);
                    }

                    $alerts++;
                } elseif ($existing) {
                    $existing->update([
                        'resolved_at' => now(),
                    ]);
                }
            }
        }

        return $alerts;
    }
}

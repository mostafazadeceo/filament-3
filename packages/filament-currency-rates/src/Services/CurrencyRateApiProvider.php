<?php

namespace Haida\FilamentCurrencyRates\Services;

use Haida\FilamentCurrencyRates\Support\NumberHelper;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CurrencyRateApiProvider
{
    public function fetch(string $url, ?string $token, int $timeout, int $retryTimes, int $retrySleepMs, array $currencies): array
    {
        if (blank($url)) {
            throw new RuntimeException('نشانی ای‌پی‌آی نرخ ارز تنظیم نشده است.');
        }

        $request = Http::acceptJson()->timeout($timeout);

        if (filled($token)) {
            $request = $request->withToken($token);
        }

        $response = $request->retry($retryTimes, $retrySleepMs, function ($exception) {
            return $exception instanceof RequestException;
        }, false)->get($url);

        if (! $response->successful()) {
            throw new RuntimeException('دریافت نرخ ارز از ای‌پی‌آی ناموفق بود.');
        }

        $payload = $response->json();
        if (! is_array($payload)) {
            throw new RuntimeException('پاسخ ای‌پی‌آی نامعتبر است.');
        }

        $data = $payload['data'] ?? $payload;

        $rates = [];
        foreach ($currencies as $code) {
            $code = strtolower($code);
            $entry = $data[$code] ?? $data[strtoupper($code)] ?? null;

            if (is_array($entry)) {
                $buy = $entry['buy'] ?? $entry['buy_price'] ?? $entry['purchase'] ?? null;
                $sell = $entry['sell'] ?? $entry['sell_price'] ?? $entry['sale'] ?? $entry['price'] ?? null;

                $rates[$code] = [
                    'code' => $code,
                    'name' => $entry['name'] ?? null,
                    'buy' => is_numeric($buy) ? (float) $buy : NumberHelper::normalize((string) $buy),
                    'sell' => is_numeric($sell) ? (float) $sell : NumberHelper::normalize((string) $sell),
                ];

                continue;
            }

            if (is_numeric($entry)) {
                $rates[$code] = [
                    'code' => $code,
                    'name' => null,
                    'buy' => null,
                    'sell' => (float) $entry,
                ];
            }
        }

        return $rates;
    }
}

<?php

namespace Haida\FilamentCurrencyRates\Services;

use DOMDocument;
use DOMXPath;
use Haida\FilamentCurrencyRates\Support\NumberHelper;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CurrencyRateScraper
{
    public function fetch(string $url, int $timeout, int $retryTimes, int $retrySleepMs, ?string $userAgent = null): array
    {
        $request = Http::withHeaders(['Accept' => 'text/html'])->timeout($timeout);

        if (filled($userAgent)) {
            $request = $request->withHeaders(['User-Agent' => $userAgent]);
        }

        $response = $request->retry($retryTimes, $retrySleepMs, function ($exception) {
            return $exception instanceof RequestException;
        }, false)->get($url);

        if (! $response->successful()) {
            throw new RuntimeException('دریافت صفحه نرخ ارز ناموفق بود.');
        }

        return $this->parseHtml($response->body());
    }

    public function parseHtml(string $html): array
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $rows = $xpath->query("//table[contains(@class,'CurrencyTbl')]//tbody//tr[@onclick]");

        $rates = [];

        if (! $rows) {
            return $rates;
        }

        foreach ($rows as $row) {
            $onclick = $row->getAttribute('onclick');
            if (! $onclick) {
                continue;
            }

            if (! preg_match('/currencies-price\/([a-z-]+)/i', $onclick, $matches)) {
                continue;
            }

            $code = strtolower($matches[1]);
            $name = trim($xpath->evaluate("string(.//td[contains(@class,'currName')])", $row));
            $buyText = trim($xpath->evaluate("string(.//td[contains(@class,'buyPrice')])", $row));
            $sellText = trim($xpath->evaluate("string(.//td[contains(@class,'sellPrice')])", $row));

            $rates[$code] = [
                'code' => $code,
                'name' => $name,
                'buy' => NumberHelper::normalize($buyText),
                'sell' => NumberHelper::normalize($sellText),
            ];
        }

        return $rates;
    }
}

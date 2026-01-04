<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoNodes\Services;

use Haida\FilamentCryptoNodes\Models\CryptoNodeConnector;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class BitcoinCoreRpcService
{
    public function getNewAddress(?string $label = null, ?string $addressType = null, ?array $config = null): string
    {
        $params = [];
        if ($label !== null) {
            $params[] = $label;
        }
        if ($addressType !== null) {
            $params[] = $addressType;
        }

        return (string) $this->call('getnewaddress', $params, $config);
    }

    public function getReceivedByAddress(string $address, int $minConf = 0, ?array $config = null): float
    {
        $result = $this->call('getreceivedbyaddress', [$address, $minConf], $config);

        return (float) $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTransaction(string $txid, bool $includeWatchOnly = true, ?array $config = null): array
    {
        $result = $this->call('gettransaction', [$txid, $includeWatchOnly], $config);

        return is_array($result) ? $result : [];
    }

    /**
     * @return array<string, mixed>
     */
    public function configForConnector(?CryptoNodeConnector $connector): array
    {
        $config = is_array($connector?->config_json) ? $connector->config_json : [];

        return $this->resolveConfig($config);
    }

    protected function call(string $method, array $params, ?array $config = null): mixed
    {
        $resolved = $this->resolveConfig($config);
        $url = (string) ($resolved['rpc_url'] ?? '');
        if ($url === '') {
            throw new RuntimeException('Bitcoin RPC url missing.');
        }

        $timeout = (int) ($resolved['timeout'] ?? 10);
        $payload = [
            'jsonrpc' => '1.0',
            'id' => 'btc',
            'method' => $method,
            'params' => $params,
        ];

        $response = Http::timeout($timeout)
            ->withBasicAuth(
                (string) ($resolved['rpc_user'] ?? ''),
                (string) ($resolved['rpc_password'] ?? ''),
            )
            ->post($url, $payload);

        $data = $response->json();
        if (! is_array($data)) {
            throw new RuntimeException('Bitcoin RPC invalid response.');
        }

        if (! empty($data['error'])) {
            $error = is_array($data['error']) ? json_encode($data['error']) : (string) $data['error'];
            throw new RuntimeException('Bitcoin RPC error: '.$error);
        }

        return $data['result'] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function resolveConfig(?array $config): array
    {
        $defaults = (array) config('filament-crypto-nodes.bitcoin_core', []);

        return array_merge($defaults, $config ?? []);
    }
}

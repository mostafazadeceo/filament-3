<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoNodes\Services;

use Haida\FilamentCryptoNodes\Models\CryptoNodeConnector;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class EvmRpcService
{
    public function newBlockFilter(?array $config = null): string
    {
        return (string) $this->call('eth_newBlockFilter', [], $config);
    }

    /**
     * @return array<int, string>
     */
    public function getFilterChanges(string $filterId, ?array $config = null): array
    {
        $result = $this->call('eth_getFilterChanges', [$filterId], $config);

        return is_array($result) ? $result : [];
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<int, mixed>
     */
    public function getLogs(array $params, ?array $config = null): array
    {
        $result = $this->call('eth_getLogs', [$params], $config);

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
            throw new RuntimeException('EVM RPC url missing.');
        }

        $timeout = (int) ($resolved['timeout'] ?? 10);
        $payload = [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => $method,
            'params' => $params,
        ];

        $response = Http::timeout($timeout)->post($url, $payload);
        $data = $response->json();

        if (! is_array($data)) {
            throw new RuntimeException('EVM RPC invalid response.');
        }

        if (isset($data['error']) && $data['error']) {
            $error = is_array($data['error']) ? json_encode($data['error']) : (string) $data['error'];
            throw new RuntimeException('EVM RPC error: '.$error);
        }

        return $data['result'] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function resolveConfig(?array $config): array
    {
        $defaults = (array) config('filament-crypto-nodes.evm', []);

        return array_merge($defaults, $config ?? []);
    }
}

<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoNodes\Services;

use Haida\FilamentCryptoNodes\Models\CryptoNodeConnector;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class NodeHealthService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function nodes(?int $tenantId = null): array
    {
        $table = config('filament-crypto-nodes.tables.node_connectors', 'crypto_node_connectors');
        if (Schema::hasTable($table)) {
            $query = CryptoNodeConnector::query();
            if ($tenantId) {
                $query->where('tenant_id', $tenantId);
            }

            $records = $query->get();
            if ($records->isNotEmpty()) {
                return $records->map(function (CryptoNodeConnector $connector) {
                    $health = $this->checkConnector($connector);

                    return [
                        'id' => $connector->getKey(),
                        'type' => $connector->type,
                        'label' => $connector->label,
                        'status' => $connector->status,
                        'health' => $health['status'] ?? 'unknown',
                        'checked_at' => $health['checked_at'] ?? now()->toIso8601String(),
                    ];
                })->all();
            }
        }

        $nodes = [];

        if (config('filament-crypto-nodes.btcpay.enabled')) {
            $nodes[] = [
                'type' => 'btcpay',
                'status' => $this->checkBtcpay([]),
            ];
        }

        if (config('filament-crypto-nodes.bitcoin_core.enabled')) {
            $nodes[] = [
                'type' => 'bitcoin_core',
                'status' => $this->checkBitcoinCore([]),
            ];
        }

        if (config('filament-crypto-nodes.evm.enabled')) {
            $nodes[] = [
                'type' => 'evm',
                'status' => $this->checkEvm([]),
            ];
        }

        return $nodes;
    }

    /**
     * @return array<string, mixed>
     */
    public function checkConnector(CryptoNodeConnector $connector): array
    {
        $config = is_array($connector->config_json) ? $connector->config_json : [];
        $type = $connector->type;

        $status = match ($type) {
            'btcpay' => $this->checkBtcpay($config),
            'bitcoin_core' => $this->checkBitcoinCore($config),
            'evm' => $this->checkEvm($config),
            default => 'unsupported',
        };

        return [
            'status' => $status,
            'checked_at' => now()->toIso8601String(),
            'type' => $type,
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function checkBtcpay(array $config): string
    {
        $baseUrl = (string) ($config['base_url'] ?? config('filament-crypto-nodes.btcpay.base_url'));
        if ($baseUrl === '') {
            return 'not_configured';
        }

        $response = Http::timeout((int) config('filament-crypto-nodes.btcpay.timeout', 10))
            ->get(rtrim($baseUrl, '/').'/api/v1/server/info');

        return $response->ok() ? 'ok' : 'error';
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function checkBitcoinCore(array $config): string
    {
        $url = (string) ($config['rpc_url'] ?? config('filament-crypto-nodes.bitcoin_core.rpc_url'));
        if ($url === '') {
            return 'not_configured';
        }

        $payload = [
            'jsonrpc' => '1.0',
            'id' => 'health',
            'method' => 'getblockchaininfo',
            'params' => [],
        ];

        $response = Http::timeout((int) config('filament-crypto-nodes.bitcoin_core.timeout', 10))
            ->withBasicAuth(
                (string) ($config['rpc_user'] ?? config('filament-crypto-nodes.bitcoin_core.rpc_user')),
                (string) ($config['rpc_password'] ?? config('filament-crypto-nodes.bitcoin_core.rpc_password')),
            )
            ->post($url, $payload);

        return $response->ok() ? 'ok' : 'error';
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function checkEvm(array $config): string
    {
        $url = (string) ($config['rpc_url'] ?? config('filament-crypto-nodes.evm.rpc_url'));
        if ($url === '') {
            return 'not_configured';
        }

        $payload = [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'eth_blockNumber',
            'params' => [],
        ];

        $response = Http::timeout((int) config('filament-crypto-nodes.evm.timeout', 10))
            ->post($url, $payload);

        return $response->ok() ? 'ok' : 'error';
    }
}

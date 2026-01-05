<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Services;

use Haida\ProvidersEsimGoCore\Clients\EsimGoClientFactory;
use Haida\ProvidersEsimGoCore\Events\EsimGoOrderReady;
use Haida\ProvidersEsimGoCore\Exceptions\EsimGoApiException;
use Haida\ProvidersEsimGoCore\Jobs\PollEsimGoAssignmentsJob;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Haida\ProvidersEsimGoCore\Models\EsimGoEsim;
use Haida\ProvidersEsimGoCore\Models\EsimGoOrder;
use Illuminate\Database\DatabaseManager;

class EsimGoOrderService
{
    public function __construct(
        protected EsimGoClientFactory $clientFactory,
        protected DatabaseManager $db,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function createProviderOrder(
        EsimGoConnection $connection,
        array $payload,
        ?int $commerceOrderId = null,
        bool $sandbox = false,
    ): EsimGoOrder {
        if ($commerceOrderId && empty($payload['force'])) {
            $existing = EsimGoOrder::query()
                ->where('tenant_id', $connection->tenant_id)
                ->where('connection_id', $connection->getKey())
                ->where('commerce_order_id', $commerceOrderId)
                ->latest('id')
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        $correlationId = app()->bound('correlation_id') ? app('correlation_id') : null;

        return $this->db->transaction(function () use ($connection, $payload, $commerceOrderId, $sandbox, $correlationId): EsimGoOrder {
            $order = EsimGoOrder::query()->create([
                'tenant_id' => $connection->tenant_id,
                'commerce_order_id' => $commerceOrderId,
                'connection_id' => $connection->getKey(),
                'status' => 'validating',
                'currency' => (string) data_get($payload, 'currency', 'USD'),
                'total' => (float) data_get($payload, 'total', 0),
                'raw_request' => $payload,
                'correlation_id' => is_string($correlationId) ? $correlationId : null,
            ]);

            $client = $this->clientFactory->make($connection, $sandbox);

            $validatePayload = array_merge($payload, ['type' => 'validate']);
            try {
                $client->createOrder($validatePayload);
            } catch (EsimGoApiException $exception) {
                $order->update([
                    'status' => 'failed',
                    'status_message' => $exception->getMessage(),
                ]);

                throw $exception;
            }

            $transactionPayload = array_merge($payload, ['type' => 'transaction']);
            $response = $client->createOrder($transactionPayload);

            $providerReference = (string) (
                data_get($response, 'reference')
                ?? data_get($response, 'orderReference')
                ?? data_get($response, 'order.reference')
                ?? data_get($response, 'data.reference')
                ?? ''
            );

            $order->update([
                'provider_reference' => $providerReference !== '' ? $providerReference : null,
                'status' => 'processing',
                'status_message' => data_get($response, 'message'),
                'raw_response' => $response,
            ]);

            $esims = $this->extractEsims($response);
            if ($esims !== []) {
                $this->storeEsims($order, $esims);
                $order->update(['status' => 'ready']);
                event(new EsimGoOrderReady($order->refresh()));
            } else {
                $order->update(['status' => 'provisioning']);
                PollEsimGoAssignmentsJob::dispatch($order->getKey())->onQueue((string) config('providers-esim-go-core.queue', 'providers'));
            }

            return $order->refresh();
        });
    }

    public function refreshAssignments(EsimGoOrder $order, bool $sandbox = false): EsimGoOrder
    {
        $connection = $this->resolveConnection($order);
        if (! $connection || ! $order->provider_reference) {
            return $order;
        }

        $client = $this->clientFactory->make($connection, $sandbox);
        $response = $client->listAssignments([
            'reference' => $order->provider_reference,
            'orderReference' => $order->provider_reference,
        ]);

        $esims = $this->extractEsims($response);
        if ($esims !== []) {
            $this->storeEsims($order, $esims);
            $order->update(['status' => 'ready']);
            event(new EsimGoOrderReady($order->refresh()));
        }

        return $order->refresh();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function extractEsims(array $payload): array
    {
        $esims = $payload['esims'] ?? $payload['assignments'] ?? data_get($payload, 'data.esims', []);

        return is_array($esims) ? $esims : [];
    }

    /**
     * @param  array<int, array<string, mixed>>  $esims
     */
    protected function storeEsims(EsimGoOrder $order, array $esims): void
    {
        foreach ($esims as $item) {
            $iccid = (string) data_get($item, 'iccid');
            if ($iccid === '') {
                continue;
            }

            EsimGoEsim::query()->updateOrCreate([
                'tenant_id' => $order->tenant_id,
                'order_id' => $order->getKey(),
                'iccid' => $iccid,
            ], [
                'matching_id' => data_get($item, 'matchingId', data_get($item, 'matching_id')),
                'smdp_address' => data_get($item, 'smdpAddress', data_get($item, 'smdp_address')),
                'state' => data_get($item, 'state', data_get($item, 'status', 'assigned')),
                'external_ref' => data_get($item, 'reference'),
                'last_refreshed_at' => now(),
            ]);
        }
    }

    protected function resolveConnection(EsimGoOrder $order): ?EsimGoConnection
    {
        if ($order->connection_id) {
            return EsimGoConnection::query()->find($order->connection_id);
        }

        return EsimGoConnection::query()->default()->first();
    }
}

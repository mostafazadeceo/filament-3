<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Support;

use Illuminate\Support\Str;

class EsimGoFakeResponse
{
    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>|null  $payload
     * @return array<string, mixed>
     */
    public static function handle(string $method, string $resource, array $query = [], ?array $payload = null): array
    {
        $runId = (string) (config('providers-esim-go-core.fake_run_id') ?: 'FAKE');
        $resource = ltrim($resource, '/');

        if (Str::startsWith($resource, 'catalogue')) {
            return [
                'data' => [
                    [
                        'name' => 'EU-1GB',
                        'description' => 'EU bundle',
                        'groups' => ['EU'],
                        'countries' => ['DE', 'FR'],
                        'dataAmount' => 1024,
                        'duration' => 7,
                        'price' => 9.5,
                        'currency' => 'USD',
                        'billingType' => 'FixedCost',
                    ],
                ],
                'pagination' => [
                    'totalPages' => 1,
                ],
            ];
        }

        if (Str::startsWith($resource, 'orders')) {
            if (Str::upper($method) === 'POST') {
                $type = $payload['type'] ?? null;
                if ($type === 'validate') {
                    return ['status' => 'ok'];
                }

                return [
                    'reference' => 'esim-' . $runId,
                    'esims' => [
                        [
                            'iccid' => '890100000000' . $runId,
                            'matchingId' => 'match-' . $runId,
                            'smdpAddress' => 'smdp.esim-go.com',
                            'state' => 'assigned',
                        ],
                    ],
                ];
            }

            return [
                'data' => [
                    [
                        'reference' => 'esim-' . $runId,
                        'status' => 'assigned',
                    ],
                ],
            ];
        }

        if (Str::startsWith($resource, 'inventory')) {
            return [
                'data' => [],
                'pagination' => [
                    'totalPages' => 1,
                ],
            ];
        }

        if (Str::startsWith($resource, 'esims/assignments')) {
            return [
                'items' => [
                    [
                        'iccid' => '890100000000' . $runId,
                        'matchingId' => 'match-' . $runId,
                        'smdpAddress' => 'smdp.esim-go.com',
                        'state' => 'assigned',
                    ],
                ],
            ];
        }

        return [
            'data' => [],
            'query' => $query,
        ];
    }
}

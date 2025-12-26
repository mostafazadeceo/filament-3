<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\ApiKey;
use Illuminate\Support\Str;

class ApiKeyService
{
    /**
     * @param  array{name: string, tenant_id?: int|null, user_id?: int|null, abilities?: array|null, scopes?: array|null, expires_at?: string|null}  $data
     * @return array{model: ApiKey, token: string}
     */
    public function create(array $data): array
    {
        $plain = Str::random(48);
        $hash = hash('sha256', $plain);

        $apiKey = ApiKey::query()->create([
            'tenant_id' => $data['tenant_id'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'name' => $data['name'],
            'token_hash' => $hash,
            'token_prefix' => substr($plain, 0, 8),
            'abilities' => $data['abilities'] ?? ['*'],
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        if (! empty($data['scopes']) && is_array($data['scopes'])) {
            $apiKey->scopes()->delete();
            foreach (array_unique($data['scopes']) as $scope) {
                $apiKey->scopes()->create(['scope' => $scope]);
            }
        }

        return ['model' => $apiKey, 'token' => $plain];
    }

    public function rotate(ApiKey $apiKey): string
    {
        $plain = Str::random(48);
        $apiKey->update([
            'token_hash' => hash('sha256', $plain),
            'token_prefix' => substr($plain, 0, 8),
            'last_used_at' => null,
        ]);

        return $plain;
    }
}

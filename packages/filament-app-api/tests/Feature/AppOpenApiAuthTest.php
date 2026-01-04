<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Tests\Feature;

use Filamat\IamSuite\Models\ApiKey;
use Filamat\IamSuite\Models\Tenant;
use Haida\FilamentAppApi\Tests\TestCase;
use Illuminate\Support\Str;

class AppOpenApiAuthTest extends TestCase
{
    public function test_openapi_rejects_missing_scope(): void
    {
        $token = 'app-api-key';
        $this->createApiKey($token, []);

        $response = $this->getJson('/api/v1/app/openapi', [
            'X-Api-Key' => $token,
        ]);

        $response->assertStatus(403);
    }

    public function test_openapi_allows_scope(): void
    {
        $token = 'app-api-key-scope';
        $this->createApiKey($token, ['app.view']);

        $response = $this->getJson('/api/v1/app/openapi', [
            'X-Api-Key' => $token,
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['openapi', 'info', 'paths']);
    }

    /**
     * @param  array<int, string>  $abilities
     */
    protected function createApiKey(string $token, array $abilities): ApiKey
    {
        $tenant = Tenant::create([
            'name' => 'Tenant',
            'slug' => Str::random(8),
        ]);

        return ApiKey::create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Test Key',
            'token_hash' => hash('sha256', $token),
            'abilities' => $abilities,
        ]);
    }
}

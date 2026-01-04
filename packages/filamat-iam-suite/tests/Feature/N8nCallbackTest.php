<?php

declare(strict_types=1);

use Filamat\IamSuite\Models\IamAiActionProposal;
use Filamat\IamSuite\Models\IamAiReport;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\Webhook;
use Filamat\IamSuite\Services\ApiKeyService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

beforeEach(function () {
    Http::fake();
});

it('accepts inbound n8n report with header token auth', function () {
    $tenant = Tenant::query()->create(['name' => 'Tenant A', 'slug' => 'a']);
    $result = app(ApiKeyService::class)->create([
        'name' => 'n8n-test',
        'tenant_id' => $tenant->getKey(),
    ]);

    config([
        'filamat-iam.automation.inbound.auth_mode' => 'header',
        'filamat-iam.automation.inbound.token' => 'token-123',
        'filamat-iam.automation.inbound.token_header' => 'X-N8N-Token',
    ]);

    $payload = [
        'idempotency_key' => (string) Str::uuid(),
        'title' => 'Risk Summary',
        'severity' => 'high',
        'report' => [
            'markdown' => 'Test report',
            'findings' => ['A', 'B'],
        ],
    ];

    $response = $this->postJson('/api/v1/iam/n8n/callback', $payload, [
        'X-Api-Key' => $result['token'],
        'X-Tenant-ID' => $tenant->getKey(),
        'X-N8N-Token' => 'token-123',
    ]);

    $response->assertOk();
    expect(IamAiReport::query()->count())->toBe(1);
});

it('accepts inbound n8n report with hmac auth', function () {
    $tenant = Tenant::query()->create(['name' => 'Tenant B', 'slug' => 'b']);
    $result = app(ApiKeyService::class)->create([
        'name' => 'n8n-hmac',
        'tenant_id' => $tenant->getKey(),
    ]);

    $connector = Webhook::query()->create([
        'tenant_id' => $tenant->getKey(),
        'type' => 'automation',
        'url' => 'https://example.com/n8n',
        'secret' => 'hmac-secret',
        'enabled' => true,
    ]);

    config([
        'filamat-iam.automation.inbound.auth_mode' => 'hmac+nonce',
    ]);

    $payload = [
        'connector_id' => $connector->getKey(),
        'idempotency_key' => (string) Str::uuid(),
        'report' => [
            'markdown' => 'Signed report',
        ],
    ];

    $timestamp = time();
    $nonce = Str::random(16);
    $body = json_encode($payload, JSON_UNESCAPED_UNICODE);
    $signature = hash_hmac('sha256', $timestamp.'.'.$nonce.'.'.$body, 'hmac-secret');

    $response = $this->postJson('/api/v1/iam/n8n/callback', $payload, [
        'X-Api-Key' => $result['token'],
        'X-Tenant-ID' => $tenant->getKey(),
        'X-Filamat-Signature' => $signature,
        'X-Filamat-Timestamp' => (string) $timestamp,
        'X-Filamat-Nonce' => $nonce,
    ]);

    $response->assertOk();
    expect(IamAiReport::query()->count())->toBe(1);
});

it('rejects duplicate idempotency keys', function () {
    $tenant = Tenant::query()->create(['name' => 'Tenant C', 'slug' => 'c']);
    $result = app(ApiKeyService::class)->create([
        'name' => 'n8n-dup',
        'tenant_id' => $tenant->getKey(),
    ]);

    config([
        'filamat-iam.automation.inbound.auth_mode' => 'header',
        'filamat-iam.automation.inbound.token' => 'dup-token',
        'filamat-iam.automation.inbound.token_header' => 'X-N8N-Token',
    ]);

    $idempotencyKey = (string) Str::uuid();
    $payload = [
        'idempotency_key' => $idempotencyKey,
        'report' => [
            'markdown' => 'Duplicate report',
        ],
    ];

    $headers = [
        'X-Api-Key' => $result['token'],
        'X-Tenant-ID' => $tenant->getKey(),
        'X-N8N-Token' => 'dup-token',
    ];

    $this->postJson('/api/v1/iam/n8n/callback', $payload, $headers)->assertOk();
    $this->postJson('/api/v1/iam/n8n/callback', $payload, $headers)->assertStatus(409);
    expect(IamAiReport::query()->count())->toBe(1);
});

it('rejects proposals when action proposals are disabled', function () {
    $tenant = Tenant::query()->create(['name' => 'Tenant D', 'slug' => 'd']);
    $result = app(ApiKeyService::class)->create([
        'name' => 'n8n-proposal',
        'tenant_id' => $tenant->getKey(),
    ]);

    config([
        'filamat-iam.automation.inbound.auth_mode' => 'header',
        'filamat-iam.automation.inbound.token' => 'proposal-token',
        'filamat-iam.automation.inbound.token_header' => 'X-N8N-Token',
        'filamat-iam.automation.action_proposals.enabled' => false,
    ]);

    $payload = [
        'idempotency_key' => (string) Str::uuid(),
        'proposal' => [
            'action_type' => 'user.suspend',
            'target' => ['user_id' => 100],
            'reason' => 'test',
        ],
    ];

    $response = $this->postJson('/api/v1/iam/n8n/callback', $payload, [
        'X-Api-Key' => $result['token'],
        'X-Tenant-ID' => $tenant->getKey(),
        'X-N8N-Token' => 'proposal-token',
    ]);

    $response->assertStatus(403);
    expect(IamAiActionProposal::query()->count())->toBe(0);
});

it('rejects inbound callbacks without tenant context', function () {
    $result = app(ApiKeyService::class)->create([
        'name' => 'n8n-no-tenant',
    ]);

    config([
        'filamat-iam.automation.inbound.auth_mode' => 'header',
        'filamat-iam.automation.inbound.token' => 'no-tenant-token',
        'filamat-iam.automation.inbound.token_header' => 'X-N8N-Token',
    ]);

    $payload = [
        'idempotency_key' => (string) Str::uuid(),
        'report' => [
            'markdown' => 'Tenant missing',
        ],
    ];

    $response = $this->postJson('/api/v1/iam/n8n/callback', $payload, [
        'X-Api-Key' => $result['token'],
        'X-N8N-Token' => 'no-tenant-token',
    ]);

    $response->assertStatus(422);
    expect(IamAiReport::query()->count())->toBe(0);
});

it('rejects inbound callbacks without automation scope', function () {
    $tenant = Tenant::query()->create(['name' => 'Tenant E', 'slug' => 'e']);
    $result = app(ApiKeyService::class)->create([
        'name' => 'n8n-no-scope',
        'tenant_id' => $tenant->getKey(),
        'abilities' => [],
    ]);

    config([
        'filamat-iam.automation.inbound.auth_mode' => 'header',
        'filamat-iam.automation.inbound.token' => 'scope-token',
        'filamat-iam.automation.inbound.token_header' => 'X-N8N-Token',
    ]);

    $payload = [
        'idempotency_key' => (string) Str::uuid(),
        'report' => [
            'markdown' => 'Scope denied',
        ],
    ];

    $response = $this->postJson('/api/v1/iam/n8n/callback', $payload, [
        'X-Api-Key' => $result['token'],
        'X-Tenant-ID' => $tenant->getKey(),
        'X-N8N-Token' => 'scope-token',
    ]);

    $response->assertStatus(403);
    expect(IamAiReport::query()->count())->toBe(0);
});

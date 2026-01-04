<?php

declare(strict_types=1);

use Filamat\IamSuite\Events\Iam\IamUserCreated;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\Webhook;
use Filamat\IamSuite\Models\WebhookDelivery;
use Filamat\IamSuite\Services\Automation\IamEventPublisher;
use Filamat\IamSuite\Services\SecurityEventService;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Support\Facades\Http;

it('dispatches automation webhooks scoped to tenant and event filters', function () {
    Http::fake();

    $tenantA = Tenant::query()->create(['name' => 'Tenant A', 'slug' => 'a']);
    $tenantB = Tenant::query()->create(['name' => 'Tenant B', 'slug' => 'b']);

    $webhookA = Webhook::query()->create([
        'tenant_id' => $tenantA->getKey(),
        'type' => 'automation',
        'url' => 'https://example.com/a',
        'secret' => 'secret-a',
        'enabled' => true,
        'events' => ['iam.user.created'],
    ]);

    $webhookB = Webhook::query()->create([
        'tenant_id' => $tenantB->getKey(),
        'type' => 'automation',
        'url' => 'https://example.com/b',
        'secret' => 'secret-b',
        'enabled' => true,
        'events' => ['iam.user.updated'],
    ]);

    TenantContext::setTenant($tenantA);

    $event = new IamUserCreated($tenantA->getKey(), [
        'subject' => ['type' => 'user', 'id' => 1],
        'context' => ['source' => 'test'],
    ]);

    app(IamEventPublisher::class)->publish($event);

    expect(WebhookDelivery::query()->where('webhook_id', $webhookA->getKey())->count())->toBe(1);
    expect(WebhookDelivery::query()->where('webhook_id', $webhookB->getKey())->count())->toBe(0);
});

it('publishes security events to automation webhooks', function () {
    Http::fake();

    $tenant = Tenant::query()->create(['name' => 'Tenant Sec', 'slug' => 'sec']);

    $webhook = Webhook::query()->create([
        'tenant_id' => $tenant->getKey(),
        'type' => 'automation',
        'url' => 'https://example.com/security',
        'secret' => 'secret-sec',
        'enabled' => true,
        'events' => ['security.auth.login.failed'],
    ]);

    app(SecurityEventService::class)->record('auth.failed', 'warning', null, $tenant, [
        'identity' => 'user@example.test',
    ]);

    expect(WebhookDelivery::query()->where('webhook_id', $webhook->getKey())->count())->toBe(1);
});

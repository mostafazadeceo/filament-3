<?php

declare(strict_types=1);

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\ImpersonationService;
use Filamat\IamSuite\Tests\Fixtures\User;

use function Pest\Laravel\actingAs;

it('creates impersonation session with restrictions', function () {
    $tenant = Tenant::query()->create(['name' => 'Impersonation Tenant', 'slug' => 'imp']);
    $actor = User::query()->create(['name' => 'Actor', 'email' => 'actor@example.com', 'password' => bcrypt('secret'), 'is_super_admin' => true]);
    $target = User::query()->create(['name' => 'Target', 'email' => 'target@example.com', 'password' => bcrypt('secret')]);

    $target->tenants()->attach($tenant->getKey(), [
        'role' => 'member',
        'status' => 'active',
        'joined_at' => now(),
    ]);

    actingAs($actor);

    $service = app(ImpersonationService::class);
    $session = $service->start($actor, $target, $tenant, 'test', 'T-2', 5, true);

    expect($service->isImpersonating())->toBeTrue();
    expect($service->canWrite())->toBeFalse();
    expect($session->tenant_id)->toBe($tenant->getKey());

    $session->update(['expires_at' => now()->subMinute()]);
    expect($service->isExpired())->toBeTrue();

    $service->stop('expired', $actor);
    expect($service->isImpersonating())->toBeFalse();

    $service->start($actor, $target, $tenant, 'test', 'T-2', 5, true);
    session()->put(\Filamat\IamSuite\Services\ImpersonationService::SESSION_IMPERSONATION_TOKEN, 'invalid');
    expect($service->isImpersonating())->toBeFalse();
});

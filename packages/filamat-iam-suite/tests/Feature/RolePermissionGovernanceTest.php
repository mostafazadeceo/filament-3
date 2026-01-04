<?php

declare(strict_types=1);

use Filamat\IamSuite\Models\AuditLog;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Tests\Fixtures\User;

use function Pest\Laravel\actingAs;

it('requires reason for role changes and audits with reason', function () {
    config(['filamat-iam.governance.require_reason' => true]);

    $tenant = Tenant::query()->create(['name' => 'Governance Tenant', 'slug' => 'gov']);
    $actor = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@gov.test',
        'password' => bcrypt('secret'),
        'is_super_admin' => true,
    ]);

    actingAs($actor);

    $response = $this->withoutMiddleware()->postJson('/api/v1/roles', [
        'name' => 'gov-role',
        'guard_name' => 'web',
        'tenant_id' => $tenant->getKey(),
    ]);

    $response->assertStatus(422);

    $response = $this->withoutMiddleware()->postJson('/api/v1/roles', [
        'name' => 'gov-role',
        'guard_name' => 'web',
        'tenant_id' => $tenant->getKey(),
        'reason' => 'policy change',
    ]);

    $response->assertStatus(201);

    $audit = AuditLog::query()->where('action', 'role.created')->latest('id')->first();
    expect($audit)->not->toBeNull();
    expect($audit->diff['reason'] ?? null)->toBe('policy change');
});

it('requires reason for permission changes and audits with reason', function () {
    config(['filamat-iam.governance.require_reason' => true]);

    $tenant = Tenant::query()->create(['name' => 'Perm Tenant', 'slug' => 'perm']);
    $actor = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@perm.test',
        'password' => bcrypt('secret'),
        'is_super_admin' => true,
    ]);

    actingAs($actor);

    $response = $this->withoutMiddleware()->postJson('/api/v1/permissions', [
        'name' => 'perm.view',
        'guard_name' => 'web',
        'tenant_id' => $tenant->getKey(),
    ]);

    $response->assertStatus(422);

    $response = $this->withoutMiddleware()->postJson('/api/v1/permissions', [
        'name' => 'perm.view',
        'guard_name' => 'web',
        'tenant_id' => $tenant->getKey(),
        'reason' => 'policy update',
    ]);

    $response->assertStatus(201);

    $audit = AuditLog::query()->where('action', 'permission.created')->latest('id')->first();
    expect($audit)->not->toBeNull();
    expect($audit->diff['reason'] ?? null)->toBe('policy update');
});

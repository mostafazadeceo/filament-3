<?php

namespace Tests\Feature\AiCore;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAiCore\Filament\Resources\AiRequestResource;
use Haida\FilamentAiCore\Models\AiRequest;

class AiRequestAuditTest extends AiCoreTestCase
{
    public function test_ai_requests_are_tenant_scoped(): void
    {
        $tenantA = $this->createTenant('Tenant A');
        $tenantB = $this->createTenant('Tenant B');

        AiRequest::query()->create([
            'tenant_id' => $tenantA->getKey(),
            'module' => 'workhub',
            'action_type' => 'summary',
            'input_hash' => 'a',
            'output_hash' => 'b',
            'status' => 'success',
            'created_at' => now(),
        ]);

        AiRequest::query()->create([
            'tenant_id' => $tenantB->getKey(),
            'module' => 'meetings',
            'action_type' => 'minutes',
            'input_hash' => 'c',
            'output_hash' => 'd',
            'status' => 'success',
            'created_at' => now(),
        ]);

        TenantContext::setTenant($tenantA);
        $this->assertSame(1, AiRequest::query()->count());

        TenantContext::setTenant($tenantB);
        $this->assertSame(1, AiRequest::query()->count());
    }

    public function test_ai_request_resource_requires_permission(): void
    {
        $tenant = $this->createTenant('Tenant C');
        TenantContext::setTenant($tenant);

        $user = $this->createUserWithPermissions($tenant, []);
        $this->actingAs($user);

        $this->assertFalse(AiRequestResource::canViewAny());

        $userWithPermission = $this->createUserWithPermissions($tenant, ['ai.audit.view']);
        $this->actingAs($userWithPermission);

        $this->assertTrue(AiRequestResource::canViewAny());
    }
}

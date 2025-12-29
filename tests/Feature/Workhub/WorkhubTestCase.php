<?php

namespace Tests\Feature\Workhub;

use App\Models\User;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\Transition;
use Haida\FilamentWorkhub\Models\Workflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

abstract class WorkhubTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'filamat-iam.subscriptions.enforce_access' => false,
        ]);
    }

    protected function tearDown(): void
    {
        TenantContext::setTenant(null);

        parent::tearDown();
    }

    protected function createTenant(string $name = 'Tenant A'): Tenant
    {
        return Tenant::query()->create([
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)).'-'.uniqid(),
            'status' => 'active',
        ]);
    }

    protected function createUserWithPermissions(Tenant $tenant, array $permissions): User
    {
        /** @var User $user */
        $user = User::factory()->create();

        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());

        $tenant->users()->syncWithoutDetaching([
            $user->getKey() => [
                'role' => 'member',
                'status' => 'active',
                'joined_at' => now(),
            ],
        ]);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
                'tenant_id' => $tenant->getKey(),
            ]);
        }

        $role = Role::firstOrCreate([
            'name' => 'workhub-admin',
            'guard_name' => 'web',
            'tenant_id' => $tenant->getKey(),
        ]);

        $role->syncPermissions($permissions);
        $user->assignRole($role);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $user;
    }

    /**
     * @return array{workflow: Workflow, todo: Status, done: Status, transition: Transition}
     */
    protected function createWorkflowWithStatuses(Tenant $tenant): array
    {
        TenantContext::setTenant($tenant);

        $workflow = Workflow::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Workflow '.$tenant->getKey(),
            'is_default' => true,
        ]);

        $todo = Status::query()->create([
            'tenant_id' => $tenant->getKey(),
            'workflow_id' => $workflow->getKey(),
            'name' => 'Todo',
            'slug' => 'todo-'.$tenant->getKey(),
            'category' => 'todo',
            'sort_order' => 1,
            'is_default' => true,
        ]);

        $done = Status::query()->create([
            'tenant_id' => $tenant->getKey(),
            'workflow_id' => $workflow->getKey(),
            'name' => 'Done',
            'slug' => 'done-'.$tenant->getKey(),
            'category' => 'done',
            'sort_order' => 2,
            'is_default' => false,
        ]);

        $transition = Transition::query()->create([
            'tenant_id' => $tenant->getKey(),
            'workflow_id' => $workflow->getKey(),
            'name' => 'Complete',
            'from_status_id' => $todo->getKey(),
            'to_status_id' => $done->getKey(),
            'is_active' => true,
            'sort_order' => 1,
        ]);

        return [
            'workflow' => $workflow,
            'todo' => $todo,
            'done' => $done,
            'transition' => $transition,
        ];
    }

    protected function createProject(Tenant $tenant, Workflow $workflow): Project
    {
        return Project::query()->create([
            'tenant_id' => $tenant->getKey(),
            'workflow_id' => $workflow->getKey(),
            'key' => 'PRJ'.$tenant->getKey(),
            'name' => 'Project '.$tenant->getKey(),
            'status' => 'active',
        ]);
    }
}

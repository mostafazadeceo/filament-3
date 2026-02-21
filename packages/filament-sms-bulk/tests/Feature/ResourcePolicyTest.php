<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Tests\Feature;

use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Policies\SmsBulkModelPolicy;
use Haida\SmsBulk\Tests\Fixtures\User;
use Haida\SmsBulk\Tests\TestCase;
use Spatie\Permission\Models\Permission;

class ResourcePolicyTest extends TestCase
{
    public function test_policy_blocks_without_permission_and_allows_with_permission(): void
    {
        $user = User::create(['name' => 'Policy', 'email' => 'policy@example.test', 'password' => bcrypt('secret')]);

        $policy = new SmsBulkModelPolicy();

        $this->assertFalse($policy->viewAny($user));

        Permission::findOrCreate('sms-bulk.view', 'web');
        $user->givePermissionTo('sms-bulk.view');

        $this->assertTrue($policy->viewAny($user));

        $record = SmsBulkProviderConnection::create([
            'tenant_id' => 1,
            'provider' => 'ippanel_edge',
            'display_name' => 'Record',
            'status' => 'active',
        ]);

        $this->assertTrue($policy->view($user, $record));
    }
}

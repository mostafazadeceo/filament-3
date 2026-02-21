<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Tests\Feature;

use Filamat\IamSuite\Http\Middleware\ApiScope;
use Filamat\IamSuite\Models\Tenant;
use Haida\SmsBulk\Filament\Pages\Admin\EdgeNumbersPage;
use Haida\SmsBulk\Filament\Pages\Admin\EdgePackagesPage;
use Haida\SmsBulk\Filament\Pages\Admin\EdgeTicketsPage;
use Haida\SmsBulk\Filament\Pages\Admin\EdgeUsersPage;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Policies\SmsBulkModelPolicy;
use Haida\SmsBulk\Tests\Fixtures\User;
use Haida\SmsBulk\Tests\TestCase;
use Illuminate\Http\Request;

class MegaSuperAdminAccessTest extends TestCase
{
    public function test_mega_super_admin_has_full_policy_and_admin_page_access(): void
    {
        $user = User::create([
            'name' => 'Mega Admin',
            'email' => 'mega@example.test',
            'password' => bcrypt('secret'),
        ]);
        $user->iam_suite_super_admin = true;

        $tenant = Tenant::query()->create([
            'name' => 'Tenant A',
            'slug' => 'tenant-a',
            'status' => 'active',
        ]);

        $record = SmsBulkProviderConnection::query()->create([
            'tenant_id' => (int) $tenant->getKey(),
            'provider' => 'ippanel_edge',
            'display_name' => 'Edge',
            'status' => 'active',
        ]);

        $policy = new SmsBulkModelPolicy();

        $this->assertTrue($policy->viewAny($user));
        $this->assertTrue($policy->create($user));
        $this->assertTrue($policy->view($user, $record));
        $this->assertTrue($policy->update($user, $record));
        $this->assertTrue($policy->delete($user, $record));

        $this->actingAs($user);

        $this->assertTrue(EdgeUsersPage::canAccess());
        $this->assertTrue(EdgePackagesPage::canAccess());
        $this->assertTrue(EdgeNumbersPage::canAccess());
        $this->assertTrue(EdgeTicketsPage::canAccess());
    }

    public function test_mega_super_admin_bypasses_api_scope_permission_gate(): void
    {
        $user = User::create([
            'name' => 'Mega API',
            'email' => 'mega-api@example.test',
            'password' => bcrypt('secret'),
        ]);
        $user->iam_suite_super_admin = true;

        $request = Request::create('/api/v1/sms-bulk/campaigns', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new ApiScope();

        $response = $middleware->handle($request, fn () => response()->noContent(), 'sms-bulk.campaign.view');

        $this->assertSame(204, $response->getStatusCode());
    }
}


<?php

declare(strict_types=1);

namespace Tests\Feature\Mailtrap;

use Filamat\IamSuite\Models\Tenant;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Services\MailtrapInboxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MailtrapInboxServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_inboxes_creates_records(): void
    {
        Http::fake([
            'https://mailtrap.io/api/accounts' => Http::response([
                ['id' => 1, 'name' => 'Main'],
            ], 200),
            'https://mailtrap.io/api/accounts/1/inboxes' => Http::response([
                [
                    'id' => 10,
                    'name' => 'Primary',
                    'status' => 'active',
                    'email_domain' => 'inbox.mailtrap.io',
                    'emails_count' => 5,
                    'emails_unread_count' => 2,
                ],
            ], 200),
        ]);

        $tenant = Tenant::query()->create([
            'name' => 'Tenant B',
            'slug' => 'tenant-b',
            'status' => 'active',
        ]);

        $connection = MailtrapConnection::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Default',
            'api_token' => 'token-123',
            'status' => 'active',
        ]);

        $count = app(MailtrapInboxService::class)->sync($connection, true);

        $this->assertSame(1, $count);
        $this->assertDatabaseHas('mailtrap_inboxes', [
            'tenant_id' => $tenant->getKey(),
            'inbox_id' => 10,
            'name' => 'Primary',
        ]);
    }
}

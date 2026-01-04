<?php

declare(strict_types=1);

namespace Tests\Feature\Mailtrap;

use Filamat\IamSuite\Models\Tenant;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapInbox;
use Haida\MailtrapCore\Services\MailtrapMessageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MailtrapMessageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_messages_creates_records_and_refreshes(): void
    {
        Http::fake([
            'https://mailtrap.io/api/accounts' => Http::response([
                ['id' => 1, 'name' => 'Main'],
            ], 200),
            'https://mailtrap.io/api/accounts/1/inboxes/10/messages' => Http::response([
                'data' => [
                    [
                        'id' => 100,
                        'subject' => 'Hello',
                        'from_email' => 'from@example.com',
                        'to_email' => 'to@example.com',
                        'is_read' => false,
                    ],
                ],
            ], 200),
            'https://mailtrap.io/api/accounts/1/inboxes/10/messages?*' => Http::response([
                'data' => [
                    [
                        'id' => 100,
                        'subject' => 'Hello',
                        'from_email' => 'from@example.com',
                        'to_email' => 'to@example.com',
                        'is_read' => false,
                    ],
                ],
            ], 200),
            'https://mailtrap.io/api/accounts/1/inboxes/10/messages/100' => Http::response([
                'id' => 100,
                'subject' => 'Hello',
                'from_email' => 'from@example.com',
                'to_email' => 'to@example.com',
                'is_read' => true,
            ], 200),
            'https://mailtrap.io/api/accounts/1/inboxes/10/messages/100/body.html' => Http::response('<p>Hello</p>', 200),
            'https://mailtrap.io/api/accounts/1/inboxes/10/messages/100/body.txt' => Http::response('Hello', 200),
            'https://mailtrap.io/api/accounts/1/inboxes/10/messages/100/attachments' => Http::response([
                ['id' => 1, 'filename' => 'test.txt'],
            ], 200),
        ]);

        $tenant = Tenant::query()->create([
            'name' => 'Tenant D',
            'slug' => 'tenant-d',
            'status' => 'active',
        ]);

        $connection = MailtrapConnection::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Default',
            'api_token' => 'token-123',
            'status' => 'active',
        ]);

        $inbox = MailtrapInbox::query()->create([
            'tenant_id' => $tenant->getKey(),
            'connection_id' => $connection->getKey(),
            'inbox_id' => 10,
            'name' => 'Primary',
            'status' => 'active',
        ]);

        $service = app(MailtrapMessageService::class);
        $rows = $service->syncMessages($connection, $inbox, []);

        $this->assertCount(1, $rows);
        $this->assertDatabaseHas('mailtrap_messages', [
            'tenant_id' => $tenant->getKey(),
            'message_id' => 100,
        ]);

        $message = $rows[0];
        $service->refreshMessageDetails($connection, $inbox, $message);

        $this->assertDatabaseHas('mailtrap_messages', [
            'tenant_id' => $tenant->getKey(),
            'message_id' => 100,
            'is_read' => 1,
        ]);
    }
}

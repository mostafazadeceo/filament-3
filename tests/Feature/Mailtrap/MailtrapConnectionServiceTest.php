<?php

declare(strict_types=1);

namespace Tests\Feature\Mailtrap;

use Filamat\IamSuite\Models\Tenant;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Services\MailtrapConnectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MailtrapConnectionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_connection_sets_account_id(): void
    {
        Http::fake([
            'https://mailtrap.io/api/accounts' => Http::response([
                ['id' => 123, 'name' => 'Main'],
            ], 200),
        ]);

        $tenant = Tenant::query()->create([
            'name' => 'Tenant A',
            'slug' => 'tenant-a',
            'status' => 'active',
        ]);

        $connection = MailtrapConnection::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Default',
            'api_token' => 'token-123',
            'status' => 'active',
        ]);

        $result = app(MailtrapConnectionService::class)->testConnection($connection);

        $this->assertSame(123, $connection->refresh()->account_id);
        $this->assertSame(123, $result['account_id']);
    }
}

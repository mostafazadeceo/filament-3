<?php

declare(strict_types=1);

namespace Tests\Feature\Mailtrap;

use Filamat\IamSuite\Models\Tenant;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Services\MailtrapDomainService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MailtrapDomainServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_domains_creates_records(): void
    {
        Http::fake([
            'https://mailtrap.io/api/accounts' => Http::response([
                ['id' => 1, 'name' => 'Main'],
            ], 200),
            'https://mailtrap.io/api/accounts/1/sending_domains' => Http::response([
                'data' => [
                    [
                        'id' => 55,
                        'domain_name' => 'example.com',
                        'dns_verified' => true,
                        'compliance_status' => 'ok',
                    ],
                ],
            ], 200),
        ]);

        $tenant = Tenant::query()->create([
            'name' => 'Tenant C',
            'slug' => 'tenant-c',
            'status' => 'active',
        ]);

        $connection = MailtrapConnection::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Default',
            'api_token' => 'token-123',
            'status' => 'active',
        ]);

        $count = app(MailtrapDomainService::class)->sync($connection, true);

        $this->assertSame(1, $count);
        $this->assertDatabaseHas('mailtrap_sending_domains', [
            'tenant_id' => $tenant->getKey(),
            'domain_id' => 55,
            'domain_name' => 'example.com',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Mailtrap;

use Haida\FilamentNotify\Mailtrap\Channels\MailtrapChannelDriver;
use Haida\FilamentNotify\Core\Support\Context\DeliveryContext;
use Haida\FilamentNotify\Core\Support\Rendering\RenderedMessage;
use Haida\FilamentNotify\Core\Support\Sending\DeliveryResult;
use Haida\FilamentNotify\Core\Support\Testing\ChannelTestContextFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MailtrapChannelDriverTest extends TestCase
{
    use RefreshDatabase;

    public function test_mailtrap_send_success(): void
    {
        Http::fake([
            'https://send.api.mailtrap.io/api/send' => Http::response(['status' => 'ok'], 202),
        ]);

        $driver = new MailtrapChannelDriver();

        $context = ChannelTestContextFactory::make(
            'admin',
            $driver->key(),
            [
                'api_token' => 'token-123',
                'from_address' => 'no-reply@example.com',
                'from_name' => 'Mailtrap',
            ],
            [
                'email' => 'test@example.com',
            ],
            [
                'panel' => [
                    'id' => 'admin',
                ],
            ],
        );

        $message = new RenderedMessage(
            subject: 'Test',
            body: 'Hello',
            meta: [],
        );

        $result = $driver->send($context, $message);

        $this->assertInstanceOf(DeliveryResult::class, $result);
        $this->assertTrue($result->success);
    }
}

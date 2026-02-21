<?php

namespace Tests\Feature\Chat;

use App\Models\User;
use Filamat\IamSuite\Models\Tenant;
use Haida\FilamentChat\Models\ChatConnection;
use Haida\FilamentChat\Models\ChatUserLink;
use Haida\FilamentChat\Services\ChatConnectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatConnectionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_user_creates_link_in_fake_mode(): void
    {
        config(['filament-chat.fake' => true]);

        $tenant = Tenant::query()->create([
            'name' => 'Tenant One',
            'slug' => 'tenant-one',
            'status' => 'active',
        ]);

        $user = User::query()->create([
            'name' => 'Chat User',
            'email' => 'chat.user@example.com',
            'password' => 'secret-password',
        ]);

        $tenant->users()->attach($user->id, [
            'status' => 'active',
        ]);

        $connection = ChatConnection::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Rocket',
            'provider' => 'rocket_chat',
            'base_url' => 'https://chat.example.test',
            'status' => 'active',
            'api_user_id' => 'admin',
            'api_token' => 'token',
        ]);

        $service = app(ChatConnectionService::class);
        $link = $service->syncUser($connection, $user);

        $this->assertInstanceOf(ChatUserLink::class, $link);
        $this->assertDatabaseHas($link->getTable(), [
            'chat_connection_id' => $connection->id,
            'user_id' => $user->id,
            'status' => 'active',
        ]);
    }

    public function test_deactivate_and_reactivate_link_in_fake_mode(): void
    {
        config(['filament-chat.fake' => true]);

        $tenant = Tenant::query()->create([
            'name' => 'Tenant Two',
            'slug' => 'tenant-two',
            'status' => 'active',
        ]);

        $user = User::query()->create([
            'name' => 'Tenant User',
            'email' => 'tenant.user@example.com',
            'password' => 'secret-password',
        ]);

        $tenant->users()->attach($user->id, [
            'status' => 'active',
        ]);

        $connection = ChatConnection::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Rocket 2',
            'provider' => 'rocket_chat',
            'base_url' => 'https://chat.example.test',
            'status' => 'active',
            'api_user_id' => 'admin',
            'api_token' => 'token',
        ]);

        $service = app(ChatConnectionService::class);
        $link = $service->syncUser($connection, $user);

        $service->deactivateLink($link->fresh());
        $this->assertDatabaseHas($link->getTable(), [
            'id' => $link->id,
            'status' => 'inactive',
        ]);

        $service->reactivateLink($link->fresh());
        $this->assertDatabaseHas($link->getTable(), [
            'id' => $link->id,
            'status' => 'active',
        ]);
    }

    public function test_reset_password_marks_metadata_in_fake_mode(): void
    {
        config(['filament-chat.fake' => true]);

        $tenant = Tenant::query()->create([
            'name' => 'Tenant Three',
            'slug' => 'tenant-three',
            'status' => 'active',
        ]);

        $user = User::query()->create([
            'name' => 'Reset User',
            'email' => 'reset.user@example.com',
            'password' => 'secret-password',
        ]);

        $tenant->users()->attach($user->id, [
            'status' => 'active',
        ]);

        $connection = ChatConnection::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Rocket 3',
            'provider' => 'rocket_chat',
            'base_url' => 'https://chat.example.test',
            'status' => 'active',
            'api_user_id' => 'admin',
            'api_token' => 'token',
        ]);

        $service = app(ChatConnectionService::class);
        $link = $service->syncUser($connection, $user);

        $service->resetLinkPassword($link->fresh(), 'NewStrongPass123');

        $this->assertDatabaseHas($link->getTable(), [
            'id' => $link->id,
            'status' => 'active',
        ]);

        $this->assertNotNull($link->fresh()?->metadata['password_reset_at'] ?? null);
    }
}

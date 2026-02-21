<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Contracts;

use App\Models\User;
use Haida\FilamentChat\Models\ChatConnection;
use Haida\FilamentChat\Models\ChatUserLink;

interface ChatProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function testConnection(ChatConnection $connection): array;

    public function upsertUser(ChatConnection $connection, User $user, ?ChatUserLink $link = null): ChatUserLink;

    public function deactivateUser(ChatConnection $connection, ChatUserLink $link): void;

    // Optional: ensure tenant-scoped structures (team/rooms) exist before user sync
    public function ensureTenantScope(ChatConnection $connection, ?\Filamat\IamSuite\Models\Tenant $tenant = null): void;

    // Optional: ensure user is placed in tenant scopes (team/rooms)
    public function ensureUserScope(ChatConnection $connection, ChatUserLink $link, ?\Filamat\IamSuite\Models\Tenant $tenant = null): void;
}

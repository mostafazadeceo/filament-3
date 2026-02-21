<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Services;

use App\Models\User;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentChat\Models\ChatConnection;
use Haida\FilamentChat\Models\ChatUserLink;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatConnectionService
{
    public function __construct(
        protected ChatProviderManager $providers,
    ) {}

    public function resolveConnection(?int $connectionId = null): ?ChatConnection
    {
        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            return null;
        }

        $query = ChatConnection::query()->where('tenant_id', $tenant->getKey());

        if ($connectionId) {
            return $query->where('id', $connectionId)->first();
        }

        return $query->default()->first();
    }

    /**
     * @return array<string, mixed>
     */
    public function testConnection(ChatConnection $connection): array
    {
        try {
            $provider = $this->providers->resolve($connection);
            $result = $provider->testConnection($connection);

            $connection->update([
                'status' => 'active',
                'last_tested_at' => now(),
                'last_error_message' => null,
                'last_error_at' => null,
            ]);

            return $result;
        } catch (\Throwable $exception) {
            $connection->update([
                'status' => 'error',
                'last_error_message' => $exception->getMessage(),
                'last_error_at' => now(),
            ]);

            throw $exception;
        }
    }

    public function syncUsers(ChatConnection $connection, ?Tenant $tenant = null): int
    {
        $tenant = $tenant ?: Tenant::query()->find($connection->tenant_id);
        if (! $tenant) {
            return 0;
        }

        $count = 0;
        $tenant->users()->orderBy('users.id')->chunk(50, function ($users) use (&$count, $connection) {
            foreach ($users as $user) {
                $this->syncUser($connection, $user);
                $count++;
            }
        });

        $connection->update([
            'last_sync_at' => now(),
        ]);

        return $count;
    }

    public function syncUser(ChatConnection $connection, User $user): ChatUserLink
    {
        if (config('filament-chat.fake', false)) {
            return $this->fakeSync($connection, $user);
        }

        $link = ChatUserLink::query()
            ->where('chat_connection_id', $connection->getKey())
            ->where('user_id', $user->getKey())
            ->first();

        // Ensure tenant-scoped resources (team/room) exist before user sync when provider supports it
        $tenant = Tenant::query()->find($connection->tenant_id);
        $provider = $this->providers->resolve($connection);

        if (method_exists($provider, 'ensureTenantScope')) {
            $provider->ensureTenantScope($connection, $tenant);
        }

        try {
            $link = $provider->upsertUser($connection, $user, $link);

            // Ensure membership in tenant-scoped room/team if provider supports it
            if (method_exists($provider, 'ensureUserScope')) {
                $provider->ensureUserScope($connection, $link, $tenant);
            }

            $link->update([
                'status' => 'active',
                'synced_at' => now(),
                'last_error_message' => null,
                'last_error_at' => null,
            ]);

            return $link;
        } catch (\Throwable $exception) {
            if (! $link) {
                $link = ChatUserLink::query()->create([
                    'tenant_id' => $connection->tenant_id,
                    'chat_connection_id' => $connection->getKey(),
                    'user_id' => $user->getKey(),
                    'status' => 'error',
                ]);
            }

            $link->update([
                'status' => 'error',
                'last_error_message' => $exception->getMessage(),
                'last_error_at' => now(),
            ]);

            throw $exception;
        }
    }

    public function rotateOidcCredentials(ChatConnection $connection): ChatConnection
    {
        $connection->forceFill([
            'oidc_client_id' => (string) Str::uuid(),
            'oidc_client_secret' => Str::random(64),
        ])->save();

        return $connection;
    }

    public function deactivateUser(ChatConnection $connection, User $user): void
    {
        $link = ChatUserLink::query()
            ->where('chat_connection_id', $connection->getKey())
            ->where('user_id', $user->getKey())
            ->first();

        if (! $link) {
            return;
        }

        if (config('filament-chat.fake', false)) {
            $link->update([
                'status' => 'inactive',
                'last_error_message' => null,
                'last_error_at' => null,
            ]);

            return;
        }

        $provider = $this->providers->resolve($connection);
        $provider->deactivateUser($connection, $link);

        $link->update([
            'status' => 'inactive',
            'last_error_message' => null,
            'last_error_at' => null,
        ]);
    }

    public function deactivateLink(ChatUserLink $link): void
    {
        $connection = $link->connection;
        $user = $link->user;
        if (! $connection || ! $user) {
            return;
        }

        $this->deactivateUser($connection, $user);
    }

    public function reactivateLink(ChatUserLink $link): void
    {
        $connection = $link->connection;
        if (! $connection) {
            return;
        }

        if (! config('filament-chat.fake', false)) {
            $provider = $this->providers->resolve($connection);
            if (method_exists($provider, 'reactivateUser')) {
                $provider->reactivateUser($connection, $link);
            }
        }

        $link->update([
            'status' => 'active',
            'synced_at' => now(),
            'last_error_message' => null,
            'last_error_at' => null,
        ]);
    }

    public function resetLinkPassword(ChatUserLink $link, string $password): void
    {
        $connection = $link->connection;
        if (! $connection) {
            return;
        }

        if (! config('filament-chat.fake', false)) {
            $provider = $this->providers->resolve($connection);
            if (! method_exists($provider, 'resetUserPassword')) {
                throw new \RuntimeException('Password reset is not supported by the active chat provider.');
            }

            $provider->resetUserPassword($connection, $link, $password);
        }

        $metadata = (array) ($link->metadata ?? []);
        $metadata['password_reset_at'] = now()->toIso8601String();
        $link->update([
            'metadata' => $metadata,
            'last_error_message' => null,
            'last_error_at' => null,
        ]);
    }

    protected function fakeSync(ChatConnection $connection, User $user): ChatUserLink
    {
        return DB::transaction(function () use ($connection, $user) {
            $link = ChatUserLink::query()->firstOrNew([
                'chat_connection_id' => $connection->getKey(),
                'user_id' => $user->getKey(),
            ]);

            $link->fill([
                'tenant_id' => $connection->tenant_id,
                'chat_user_id' => 'fake-'.$user->getKey(),
                'username' => $this->resolveUsername($user),
                'status' => 'active',
                'synced_at' => now(),
                'last_error_message' => null,
                'last_error_at' => null,
            ]);

            $link->save();

            return $link;
        });
    }

    protected function resolveUsername(User $user): string
    {
        $email = (string) $user->email;
        $prefix = $email !== '' ? Str::before($email, '@') : 'user';
        $prefix = Str::slug($prefix);
        if ($prefix === '') {
            $prefix = 'user';
        }

        return $prefix.'.'.$user->getKey();
    }
}

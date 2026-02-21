<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Services\Providers;

use App\Models\User;
use Haida\FilamentChat\Contracts\ChatProviderInterface;
use Haida\FilamentChat\Models\ChatConnection;
use Haida\FilamentChat\Models\ChatUserLink;
use Filamat\IamSuite\Models\Tenant;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use RuntimeException;

class RocketChatProvider implements ChatProviderInterface
{
    // Ensures a tenant-scoped Team (RC workspace) exists for the connection
    public function ensureTenantScope(ChatConnection $connection, ?Tenant $tenant = null): void
    {
        $client = new RocketChatClient($connection);

        $teamName = $this->teamName($connection, $tenant);
        $storedTeamId = (string) data_get($connection->settings, 'team.id', '');

        if ($storedTeamId !== '') {
            $stored = $this->findTeamById($client, $storedTeamId);
            if ($stored) {
                $this->storeTeamMeta($connection, $stored);
                return;
            }
        }

        // Try to find existing team
        $existing = $this->findTeamByName($client, $teamName);
        if (! $existing) {
            $existing = $this->findTeamByMainRoomName($client, $teamName);
        }
        if ($existing) {
            $this->storeTeamMeta($connection, $existing);
            return;
        }

        // Create team if not found
        try {
            $created = $client->post('/api/v1/teams.create', [
                'name' => $teamName,
                'type' => 0, // 0 = public team, required by API for creation; visibility handled by membership
            ]);
        } catch (RuntimeException $exception) {
            // When the service account is not a team member, list APIs may not discover the team.
            // If the name already exists, recover by resolving team from its main room name.
            if (str_contains(strtolower($exception->getMessage()), 'team-name-already-exists')) {
                $existing = $this->findTeamByMainRoomName($client, $teamName);
                if ($existing) {
                    $this->storeTeamMeta($connection, $existing);
                    return;
                }
            }

            throw $exception;
        }

        $team = $created['team'] ?? [];
        if (! is_array($team) || empty($team['_id'])) {
            throw new RuntimeException('Unable to create team in Rocket.Chat');
        }

        $this->storeTeamMeta($connection, $team);
    }

    // Ensures the user is inside the tenant team and a default room exists
    public function ensureUserScope(ChatConnection $connection, ChatUserLink $link, ?Tenant $tenant = null): void
    {
        $client = new RocketChatClient($connection);
        $teamId = data_get($connection->settings, 'team.id');
        $teamName = data_get($connection->settings, 'team.name');

        if (! $teamId || ! $teamName) {
            $this->ensureTenantScope($connection, $tenant);
            $teamId = data_get($connection->settings, 'team.id');
            $teamName = data_get($connection->settings, 'team.name');
        }

        if (! $teamId || ! $teamName) {
            throw new RuntimeException('Tenant team is missing; cannot scope user.');
        }

        // Add user to team (ignore if already member)
        try {
            $client->post('/api/v1/teams.addMembers', [
                'teamId' => $teamId,
                'members' => [
                    [
                        'userId' => $link->chat_user_id,
                    ],
                ],
            ]);
        } catch (RuntimeException $exception) {
            if (! $this->isAlreadyMemberError($exception->getMessage())) {
                throw $exception;
            }
        }

        $user = $link->relationLoaded('user')
            ? $link->user
            : User::query()->find($link->user_id);

        if ($user instanceof User) {
            $this->syncTeamMemberRoles($client, $connection, (string) $teamId, $link, $user, $tenant);
            $this->maybeRemoveServiceAccountFromTeam($client, $connection, (string) $teamId, $link, $user, $tenant);
        }

        $this->ensureNoPasswordChangeRequired($client, $link);

        // Use the team's main room for tenant default chat
        $roomId = data_get($connection->settings, 'team.room_id');
        if (! $roomId && $teamName) {
            $team = $this->findTeamByName($client, (string) $teamName);
            if ($team) {
                $this->storeTeamMeta($connection, $team);
                $roomId = data_get($connection->settings, 'team.room_id');
            }
        }

        if ($roomId) {
            try {
                $client->post('/api/v1/channels.invite', [
                    'roomId' => $roomId,
                    'userId' => $link->chat_user_id,
                ]);
            } catch (RuntimeException $exception) {
                if (! $this->isAlreadyMemberError($exception->getMessage())) {
                    throw $exception;
                }
            }
        }
    }

    public function testConnection(ChatConnection $connection): array
    {
        $client = new RocketChatClient($connection);

        return $client->get('/api/v1/me');
    }

    public function upsertUser(ChatConnection $connection, User $user, ?ChatUserLink $link = null): ChatUserLink
    {
        $client = new RocketChatClient($connection);
        $username = $link?->username ?: $this->resolveUsername($user);

        if (! $user->email) {
            throw new \RuntimeException('Email is required for Rocket.Chat provisioning.');
        }

        $existing = $this->findUserByUsername($client, $username);
        if (! $existing && $user->email) {
            $existing = $this->findUserByEmail($client, (string) $user->email);
        }

        $tenant = Tenant::query()->find($connection->tenant_id);
        $existing = $this->sanitizeExistingUser($existing, $connection, $user);

        if ($existing) {
            $existingId = (string) ($existing['_id'] ?? $existing['id'] ?? '');
            if (! isset($existing['username']) && $existingId !== '') {
                $byId = $this->findUserById($client, $existingId);
                if (is_array($byId)) {
                    $existing = array_merge($existing, $byId);
                }
            }

            $this->syncExistingUser($client, $existing, $user, $connection, $tenant);
            return $this->updateLink($connection, $user, $existing, $username, $link);
        }

        $this->assertUserQuota($connection, $tenant, (int) $user->getKey());

        $payload = [
            'name' => (string) ($user->name ?: $username),
            'email' => (string) $user->email,
            'username' => $username,
            'password' => $this->makePassword(),
            'active' => true,
            'roles' => $this->resolveRoles($connection, $user, $tenant),
        ];

        try {
            $response = $client->post('/api/v1/users.create', $payload);
        } catch (RuntimeException $exception) {
            if (! $this->isRoleAssignmentError($exception->getMessage())) {
                throw $exception;
            }

            unset($payload['roles']);
            $response = $client->post('/api/v1/users.create', $payload);
        }
        $created = (array) ($response['user'] ?? []);

        return $this->updateLink($connection, $user, $created, $username, $link);
    }

    public function deactivateUser(ChatConnection $connection, ChatUserLink $link): void
    {
        if (! $link->chat_user_id) {
            return;
        }

        $client = new RocketChatClient($connection);

        $client->post('/api/v1/users.update', [
            'userId' => $link->chat_user_id,
            'data' => [
                'active' => false,
            ],
        ]);
    }

    public function reactivateUser(ChatConnection $connection, ChatUserLink $link): void
    {
        if (! $link->chat_user_id) {
            return;
        }

        $client = new RocketChatClient($connection);

        $client->post('/api/v1/users.update', [
            'userId' => $link->chat_user_id,
            'data' => [
                'active' => true,
                'requirePasswordChange' => false,
            ],
        ]);
    }

    public function resetUserPassword(ChatConnection $connection, ChatUserLink $link, string $password): void
    {
        if (! $link->chat_user_id) {
            return;
        }

        $password = trim($password);
        if ($password === '') {
            throw new RuntimeException('Password cannot be empty.');
        }

        $client = new RocketChatClient($connection);

        $client->post('/api/v1/users.update', [
            'userId' => $link->chat_user_id,
            'data' => [
                'password' => $password,
                'active' => true,
                'requirePasswordChange' => false,
            ],
        ]);
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

    protected function teamName(ChatConnection $connection, ?Tenant $tenant): string
    {
        $prefix = $this->teamPrefix($connection, $tenant);

        if ($tenant && $tenant->slug) {
            return $prefix.$tenant->slug;
        }
        if ($tenant && $tenant->id) {
            return $prefix.$tenant->id;
        }
        return $prefix.'default';
    }

    protected function roomName(ChatConnection $connection, ?Tenant $tenant): string
    {
        $prefix = $this->roomPrefix($connection, $tenant);

        if ($tenant && $tenant->slug) {
            return $prefix.$tenant->slug;
        }
        if ($tenant && $tenant->id) {
            return $prefix.$tenant->id;
        }
        return $prefix.'default';
    }

    /** @return array<string,mixed>|null */
    protected function findTeamByName(RocketChatClient $client, string $name): ?array
    {
        $resp = $client->get('/api/v1/teams.list', ['query' => json_encode(['name' => $name])]);
        $teams = $resp['teams'] ?? [];
        foreach ((array) $teams as $team) {
            if (is_array($team) && ($team['name'] ?? null) === $name) {
                return $team;
            }
        }
        return null;
    }

    /** @return array<string,mixed>|null */
    protected function findTeamById(RocketChatClient $client, string $teamId): ?array
    {
        try {
            $resp = $client->get('/api/v1/teams.info', ['teamId' => $teamId]);
        } catch (\Throwable) {
            return null;
        }

        $team = $resp['teamInfo'] ?? null;
        return is_array($team) ? $team : null;
    }

    /** @return array<string,mixed>|null */
    protected function findTeamByMainRoomName(RocketChatClient $client, string $roomName): ?array
    {
        try {
            $channelResp = $client->get('/api/v1/channels.info', ['roomName' => $roomName]);
        } catch (\Throwable) {
            return null;
        }

        $teamId = (string) data_get($channelResp, 'channel.teamId', '');
        if ($teamId === '') {
            return null;
        }

        return $this->findTeamById($client, $teamId);
    }

    /** @return array<string,mixed>|null */
    protected function findRoomByName(RocketChatClient $client, string $name): ?array
    {
        try {
            $resp = $client->get('/api/v1/rooms.get', ['roomName' => $name]);
            $room = $resp['room'] ?? null;
            return is_array($room) ? $room : null;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function storeTeamMeta(ChatConnection $connection, array $team): void
    {
        $settings = (array) ($connection->settings ?? []);
        $settings['team'] = [
            'id' => $team['_id'] ?? $team['id'] ?? null,
            'name' => $team['name'] ?? null,
            'room_id' => $team['roomId'] ?? null,
        ];
        $connection->settings = $settings;
        $connection->save();
    }

    /**
     * @return array<int, string>
     */
    protected function resolveRoles(ChatConnection $connection, ?User $user = null, ?Tenant $tenant = null): array
    {
        $mapped = $this->resolveMappedRoles($connection, $user, $tenant);
        if ($mapped !== []) {
            return $mapped;
        }

        $roles = $connection->settings['default_roles'] ?? null;
        if (is_array($roles) && $roles !== []) {
            return array_values(array_unique(array_filter($roles)));
        }

        $defaultRoles = config('filament-chat.providers.rocket_chat.default_roles', ['user']);
        return is_array($defaultRoles) ? $defaultRoles : ['user'];
    }

    /**
     * @return array<int, string>
     */
    protected function resolveMappedRoles(ChatConnection $connection, ?User $user, ?Tenant $tenant): array
    {
        $roleMap = $this->resolveRoleMap($connection);
        if ($roleMap === [] || ! $user || ! $tenant) {
            return [];
        }

        $iamRoles = $this->resolveIamRoles($user, $tenant);
        if ($iamRoles === []) {
            return [];
        }

        $mapped = [];
        foreach ($iamRoles as $iamRole) {
            $rcRoles = $roleMap[$iamRole] ?? null;
            if (is_string($rcRoles) && $rcRoles !== '') {
                $mapped[] = $rcRoles;
            } elseif (is_array($rcRoles)) {
                foreach ($rcRoles as $rcRole) {
                    if (is_string($rcRole) && $rcRole !== '') {
                        $mapped[] = $rcRole;
                    }
                }
            }
        }

        $mapped = array_values(array_unique(array_filter($mapped)));

        // Keep tenant owner elevation at team membership scope only.
        // Never assign global Rocket.Chat owner/moderator roles in a shared workspace.
        if (in_array('tenant_owner', $iamRoles, true)) {
            $mapped = array_values(array_diff($mapped, ['owner', 'moderator']));
            if ($mapped === []) {
                $mapped = ['user'];
            }
        }

        // Keep Rocket.Chat baseline capabilities stable even when a custom map omits `user`.
        if (! in_array('user', $mapped, true)) {
            $mapped[] = 'user';
        }

        return $mapped;
    }

    /**
     * @return array<int, string>
     */
    protected function resolveIamRoles(User $user, Tenant $tenant): array
    {
        $roles = [];

        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());

        if (method_exists($user, 'getRoleNames')) {
            $roles = $user->getRoleNames()->all();
        }

        if ($roles === []) {
            $pivotRole = $tenant->users()
                ->where('users.id', $user->getKey())
                ->first()?->pivot?->role;

            $mapped = $this->roleNameFromPivot((string) $pivotRole);
            if ($mapped) {
                $roles[] = $mapped;
            }
        }

        $roles = array_values(array_unique(array_filter(array_map('strval', $roles))));

        return $roles;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    protected function resolveRoleMap(ChatConnection $connection): array
    {
        $map = $connection->settings['role_map'] ?? null;
        if (is_array($map) && $map !== []) {
            return $map;
        }

        $defaultMap = config('filament-chat.providers.rocket_chat.role_map', []);
        return is_array($defaultMap) ? $defaultMap : [];
    }

    protected function roleNameFromPivot(string $pivotRole): ?string
    {
        return match ($pivotRole) {
            'owner' => 'tenant_owner',
            'admin' => 'tenant_admin',
            'member' => 'tenant_member',
            default => null,
        };
    }

    protected function teamPrefix(ChatConnection $connection, ?Tenant $tenant): string
    {
        $prefix = data_get($connection->settings, 'team_prefix');
        if (is_string($prefix) && $prefix !== '') {
            return $prefix;
        }

        $prefix = data_get($tenant?->settings, 'chat.team_prefix');
        if (is_string($prefix) && $prefix !== '') {
            return $prefix;
        }

        $fallback = config('filament-chat.providers.rocket_chat.team_prefix', 'tenant-');
        return is_string($fallback) && $fallback !== '' ? $fallback : 'tenant-';
    }

    protected function roomPrefix(ChatConnection $connection, ?Tenant $tenant): string
    {
        $prefix = data_get($connection->settings, 'room_prefix');
        if (is_string($prefix) && $prefix !== '') {
            return $prefix;
        }

        $prefix = data_get($tenant?->settings, 'chat.room_prefix');
        if (is_string($prefix) && $prefix !== '') {
            return $prefix;
        }

        $fallback = config('filament-chat.providers.rocket_chat.room_prefix', 'room-');
        return is_string($fallback) && $fallback !== '' ? $fallback : 'room-';
    }

    protected function tenantOwnerManageEnabled(ChatConnection $connection, ?Tenant $tenant): bool
    {
        $configured = data_get($connection->settings, 'allow_owner_manage');
        if (is_bool($configured)) {
            return $configured;
        }

        $featureFlag = (string) config('filamat-iam.chat.owner_manage_flag', 'tenant_owner_manage');
        $flags = Arr::wrap(data_get($tenant?->organization?->settings, 'entitlements.feature_flags.chat', []));
        $flags = array_values(array_filter(array_map('strval', $flags)));

        if ($flags === []) {
            return true;
        }

        return in_array($featureFlag, $flags, true);
    }

    protected function syncTeamMemberRoles(
        RocketChatClient $client,
        ChatConnection $connection,
        string $teamId,
        ChatUserLink $link,
        User $user,
        ?Tenant $tenant,
    ): void {
        if (! $link->chat_user_id || ! $tenant) {
            return;
        }

        $iamRoles = $this->resolveIamRoles($user, $tenant);
        $isTenantOwner = in_array('tenant_owner', $iamRoles, true);
        $memberRoles = ($isTenantOwner && $this->tenantOwnerManageEnabled($connection, $tenant)) ? ['owner'] : [];

        try {
            $client->post('/api/v1/teams.updateMember', [
                'teamId' => $teamId,
                'member' => [
                    'userId' => $link->chat_user_id,
                    'roles' => $memberRoles,
                ],
            ]);
        } catch (\Throwable) {
            // Keep sync resilient even if team-role API is unavailable on the Rocket.Chat node.
        }
    }

    protected function maybeRemoveServiceAccountFromTeam(
        RocketChatClient $client,
        ChatConnection $connection,
        string $teamId,
        ChatUserLink $link,
        User $user,
        ?Tenant $tenant,
    ): void {
        if (! (bool) data_get($connection->settings, 'remove_service_account_from_team', false)) {
            return;
        }

        if (! $tenant || ! $link->chat_user_id) {
            return;
        }

        $apiUserId = (string) ($connection->api_user_id ?? '');
        if ($apiUserId === '' || hash_equals($apiUserId, (string) $link->chat_user_id)) {
            return;
        }

        // Only remove the service account if the tenant owner is allowed to manage chat and becomes a team owner.
        $iamRoles = $this->resolveIamRoles($user, $tenant);
        $isTenantOwner = in_array('tenant_owner', $iamRoles, true);
        if (! ($isTenantOwner && $this->tenantOwnerManageEnabled($connection, $tenant))) {
            return;
        }

        try {
            $client->post('/api/v1/teams.removeMember', [
                'teamId' => $teamId,
                'userId' => $apiUserId,
            ]);
        } catch (\Throwable) {
            // Ignore: the API service account may already be removed, or the node may prevent removal in edge cases.
        }
    }

    protected function ensureNoPasswordChangeRequired(RocketChatClient $client, ChatUserLink $link): void
    {
        if (! $link->chat_user_id) {
            return;
        }

        try {
            $client->post('/api/v1/users.update', [
                'userId' => $link->chat_user_id,
                'data' => [
                    'requirePasswordChange' => false,
                ],
            ]);
        } catch (\Throwable) {
            // If the node disallows this field or the user doesn't exist yet, ignore.
        }
    }

    protected function makePassword(): string
    {
        return bin2hex(random_bytes(18));
    }

    /**
     * @param array<string, mixed> $existing
     */
    protected function syncExistingUser(RocketChatClient $client, array $existing, User $user, ChatConnection $connection, ?Tenant $tenant): void
    {
        if (! (bool) config('filament-chat.providers.rocket_chat.sync_profile', true)) {
            return;
        }

        $data = [];
        $existingName = (string) ($existing['name'] ?? '');
        $existingEmail = (string) (($existing['emails'][0]['address'] ?? '') ?: ($existing['email'] ?? ''));

        if ($user->name && $existingName !== (string) $user->name) {
            $data['name'] = (string) $user->name;
        }

        if ($user->email && $existingEmail !== (string) $user->email) {
            $data['email'] = (string) $user->email;
        }

        if ((bool) config('filament-chat.providers.rocket_chat.sync_roles', false)) {
            $data['roles'] = $this->resolveRoles($connection, $user, $tenant);
        }

        if ($data === []) {
            return;
        }

        $userId = (string) ($existing['_id'] ?? $existing['id'] ?? '');
        if ($userId === '') {
            return;
        }

        try {
            $client->post('/api/v1/users.update', [
                'userId' => $userId,
                'data' => $data,
            ]);
        } catch (RuntimeException $exception) {
            if (! isset($data['roles']) || ! $this->isRoleAssignmentError($exception->getMessage())) {
                throw $exception;
            }

            unset($data['roles']);
            if ($data === []) {
                return;
            }

            $client->post('/api/v1/users.update', [
                'userId' => $userId,
                'data' => $data,
            ]);
        }
    }

    protected function isAlreadyMemberError(string $message): bool
    {
        $message = strtolower($message);
        return str_contains($message, 'already')
            || str_contains($message, 'error-user-already-in-room')
            || str_contains($message, 'user already in room');
    }

    protected function isRoleAssignmentError(string $message): bool
    {
        $message = strtolower($message);

        return str_contains($message, 'assign roles is not allowed')
            || str_contains($message, 'error-action-not-allowed');
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function findUserByUsername(RocketChatClient $client, string $username): ?array
    {
        try {
            $response = $client->get('/api/v1/users.info', [
                'username' => $username,
            ]);
        } catch (\Throwable) {
            return null;
        }

        $user = $response['user'] ?? null;
        return is_array($user) ? $user : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function findUserById(RocketChatClient $client, string $userId): ?array
    {
        try {
            $response = $client->get('/api/v1/users.info', [
                'userId' => $userId,
            ]);
        } catch (\Throwable) {
            return null;
        }

        $user = $response['user'] ?? null;
        return is_array($user) ? $user : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function findUserByEmail(RocketChatClient $client, string $email): ?array
    {
        $query = json_encode([
            'emails.address' => $email,
        ]);

        if (! is_string($query)) {
            return null;
        }

        try {
            $response = $client->get('/api/v1/users.list', [
                'count' => 50,
                'query' => $query,
            ]);
        } catch (\Throwable) {
            return null;
        }

        $users = $response['users'] ?? [];
        if (! is_array($users) || $users === []) {
            return null;
        }

        $needle = strtolower($email);
        foreach ($users as $candidate) {
            if (! is_array($candidate)) {
                continue;
            }

            $candidateEmail = strtolower((string) (($candidate['emails'][0]['address'] ?? '') ?: ($candidate['email'] ?? '')));
            if ($candidateEmail === $needle) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>|null  $existing
     * @return array<string, mixed>|null
     */
    protected function sanitizeExistingUser(?array $existing, ChatConnection $connection, User $user): ?array
    {
        if (! is_array($existing)) {
            return null;
        }

        $existingId = (string) ($existing['_id'] ?? $existing['id'] ?? '');
        $apiUserId = (string) ($connection->api_user_id ?? '');
        if ($existingId === '' || $apiUserId === '' || ! hash_equals($existingId, $apiUserId)) {
            return $existing;
        }

        $existingEmail = strtolower((string) (($existing['emails'][0]['address'] ?? '') ?: ($existing['email'] ?? '')));
        $userEmail = strtolower((string) ($user->email ?? ''));

        // Do not attach normal tenant members to the API service account.
        if ($existingEmail === '' || $userEmail === '' || ! hash_equals($existingEmail, $userEmail)) {
            return null;
        }

        return $existing;
    }

    protected function assertUserQuota(ChatConnection $connection, ?Tenant $tenant, int $userId): void
    {
        $limit = $this->resolveMaxUsers($connection, $tenant);
        if (! $limit) {
            return;
        }

        $activeUsers = ChatUserLink::query()
            ->where('chat_connection_id', $connection->getKey())
            ->where('status', 'active')
            ->where('user_id', '!=', $userId)
            ->count();

        if ($activeUsers >= $limit) {
            throw new RuntimeException('Chat user quota exceeded for this organization.');
        }
    }

    protected function resolveMaxUsers(ChatConnection $connection, ?Tenant $tenant): ?int
    {
        $status = (string) data_get($tenant?->organization?->settings, 'entitlements.status', 'active');
        $bucket = $status === 'trial' ? 'trial' : 'plan';

        $maxUsers = (int) data_get($connection->settings, "quotas.{$bucket}.max_users", 0);
        if ($maxUsers <= 0) {
            $maxUsers = (int) data_get($tenant?->organization?->settings, "entitlements.quotas.chat.{$bucket}.max_users", 0);
        }

        if ($maxUsers <= 0) {
            $maxUsers = (int) data_get($tenant?->organization?->settings, 'entitlements.max_users', 0);
        }

        return $maxUsers > 0 ? $maxUsers : null;
    }

    protected function updateLink(
        ChatConnection $connection,
        User $user,
        array $chatUser,
        string $username,
        ?ChatUserLink $link,
    ): ChatUserLink {
        $link = $link ?: new ChatUserLink();

        $link->fill([
            'tenant_id' => $connection->tenant_id,
            'chat_connection_id' => $connection->getKey(),
            'user_id' => $user->getKey(),
            'chat_user_id' => (string) ($chatUser['_id'] ?? $chatUser['id'] ?? $link->chat_user_id),
            // Prefer the provider username when available to avoid stale local aliases.
            'username' => (string) ($chatUser['username'] ?? $username),
            'metadata' => array_merge($link->metadata ?? [], [
                'email' => $chatUser['emails'][0]['address'] ?? $user->email,
                'name' => $chatUser['name'] ?? $user->name,
                'roles' => $chatUser['roles'] ?? [],
            ]),
        ]);

        $link->save();

        return $link;
    }
}

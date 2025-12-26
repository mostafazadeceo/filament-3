<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\PermissionSnapshot;
use Filamat\IamSuite\Models\Tenant;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\Permission\Models\Permission;

class PermissionSnapshotService
{
    public function __construct(protected AccessService $accessService) {}

    /**
     * @param  array<string, mixed>  $meta
     */
    public function capture(Authenticatable $user, Tenant $tenant, string $source = 'manual', array $meta = []): PermissionSnapshot
    {
        if (! (bool) config('filamat-iam.features.permission_snapshots', true)) {
            throw new \RuntimeException('اسنپ‌شات مجوز غیرفعال است.');
        }

        $permissions = $this->collectPermissions($tenant);
        $effective = [];

        foreach ($permissions as $permissionKey) {
            if ($this->accessService->checkPermission($user, $tenant, $permissionKey)) {
                $effective[] = $permissionKey;
            }
        }

        $effective = array_values(array_unique($effective));
        sort($effective);

        $algo = (string) config('filamat-iam.permission_snapshots.hash_algo', 'sha256');
        $hash = hash($algo, json_encode($effective, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '');

        return PermissionSnapshot::query()->create([
            'tenant_id' => $tenant->getKey(),
            'user_id' => $user->getAuthIdentifier(),
            'hash' => $hash,
            'source' => $source,
            'permissions' => $effective,
            'meta' => $meta,
        ]);
    }

    /**
     * @return array{added: array<int, string>, removed: array<int, string>, unchanged: array<int, string>}
     */
    public function diff(PermissionSnapshot $from, PermissionSnapshot $to): array
    {
        $fromPerms = $from->permissions ?? [];
        $toPerms = $to->permissions ?? [];

        $added = array_values(array_diff($toPerms, $fromPerms));
        $removed = array_values(array_diff($fromPerms, $toPerms));
        $unchanged = array_values(array_intersect($fromPerms, $toPerms));

        sort($added);
        sort($removed);
        sort($unchanged);

        return [
            'added' => $added,
            'removed' => $removed,
            'unchanged' => $unchanged,
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function collectPermissions(Tenant $tenant): array
    {
        return Permission::query()
            ->whereNull('tenant_id')
            ->orWhere('tenant_id', $tenant->getKey())
            ->pluck('name')
            ->all();
    }
}

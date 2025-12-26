<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Jobs\SyncCapabilitiesJob;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Throwable;

class CapabilitySyncService
{
    public function __construct(protected CapabilityRegistryInterface $registry) {}

    public function markDirty(): void
    {
        if (! $this->enabled()) {
            return;
        }

        if (! $this->cacheAvailable()) {
            return;
        }

        Cache::put($this->dirtyKey(), true, $this->dirtyTtlSeconds());
    }

    public function autoSyncIfNeeded(): void
    {
        if (! $this->enabled() || ! $this->autoSyncOnBoot()) {
            return;
        }

        if (! $this->cacheAvailable()) {
            return;
        }

        if (! $this->shouldRunInContext()) {
            return;
        }

        if (! Cache::get($this->dirtyKey(), false)) {
            return;
        }

        $lastSync = (int) Cache::get($this->lastSyncKey(), 0);
        if ($lastSync > 0 && (time() - $lastSync) < $this->cooldownSeconds()) {
            return;
        }

        $this->syncWithLock();
    }

    public function syncWithLock(?string $guard = null): int
    {
        if (! $this->cacheAvailable()) {
            return $this->sync($guard);
        }

        $lock = Cache::lock($this->lockKey(), $this->lockSeconds());
        if (! $lock->get()) {
            return 0;
        }

        try {
            if ($this->queueEnabled()) {
                SyncCapabilitiesJob::dispatch($guard);

                return 0;
            }

            return $this->sync($guard);
        } finally {
            $lock->release();
        }
    }

    public function sync(?string $guard = null): int
    {
        try {
            if (! Schema::hasTable('permissions')) {
                return 0;
            }
        } catch (Throwable) {
            return 0;
        }

        $guard = $guard ?: $this->defaultGuard();
        $count = 0;

        foreach ($this->registry->all() as $capability) {
            foreach ($capability->permissions as $permissionKey) {
                Permission::query()->updateOrCreate([
                    'name' => $permissionKey,
                    'guard_name' => $guard,
                    'tenant_id' => null,
                ]);
                $count++;
            }
        }

        if (config('filamat-iam.enable_shield') && Artisan::has('shield:generate')) {
            Artisan::call('shield:generate', ['--ignore-existing' => true]);
        }

        Cache::put($this->lastSyncKey(), time(), max(600, $this->cooldownSeconds() * 2));
        Cache::forget($this->dirtyKey());

        return $count;
    }

    protected function enabled(): bool
    {
        return (bool) config('filamat-iam.capability_sync.enabled', true);
    }

    protected function autoSyncOnBoot(): bool
    {
        return (bool) config('filamat-iam.capability_sync.on_boot', true);
    }

    protected function shouldRunInContext(): bool
    {
        if (app()->runningInConsole()) {
            return (bool) config('filamat-iam.capability_sync.on_console', true);
        }

        return (bool) config('filamat-iam.capability_sync.on_http', true);
    }

    protected function queueEnabled(): bool
    {
        return (bool) config('filamat-iam.capability_sync.queue', false);
    }

    protected function lockSeconds(): int
    {
        return max(5, (int) config('filamat-iam.capability_sync.lock_seconds', 30));
    }

    protected function cooldownSeconds(): int
    {
        return max(30, (int) config('filamat-iam.capability_sync.cooldown_seconds', 300));
    }

    protected function dirtyTtlSeconds(): int
    {
        return max(300, (int) config('filamat-iam.capability_sync.dirty_ttl_seconds', 3600));
    }

    protected function cacheAvailable(): bool
    {
        try {
            Cache::store();

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    protected function defaultGuard(): string
    {
        return (string) config('filamat-iam.capability_sync.guard', 'web');
    }

    protected function dirtyKey(): string
    {
        return 'filamat_iam.capability_sync.dirty';
    }

    protected function lastSyncKey(): string
    {
        return 'filamat_iam.capability_sync.last_sync';
    }

    protected function lockKey(): string
    {
        return 'filamat_iam.capability_sync.lock';
    }
}

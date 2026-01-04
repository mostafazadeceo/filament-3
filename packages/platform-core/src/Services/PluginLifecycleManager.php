<?php

namespace Haida\PlatformCore\Services;

use Carbon\CarbonInterface;
use Haida\PlatformCore\Exceptions\PluginLifecycleException;
use Haida\PlatformCore\Models\PluginMigration;
use Haida\PlatformCore\Models\PluginRegistry;
use Haida\PlatformCore\Models\TenantPlugin;
use Haida\PlatformCore\Support\PluginManifest;
use Illuminate\Support\Facades\DB;

class PluginLifecycleManager
{
    public function install(string $pluginKey, PluginManifest $manifest): PluginRegistry
    {
        return DB::transaction(function () use ($pluginKey, $manifest) {
            $registry = PluginRegistry::query()->firstOrNew(['plugin_key' => $pluginKey]);

            $registry->name_fa = $manifest->nameFa;
            $registry->description_fa = $manifest->descriptionFa;
            $registry->version = $manifest->version;
            $registry->created_at_jalali = $manifest->createdAtJalali;
            $registry->metadata = $manifest->meta;
            $registry->status = PluginRegistry::STATUS_INSTALLED;

            if (! $registry->installed_at) {
                $registry->installed_at = now();
            }

            $registry->save();

            return $registry->fresh();
        });
    }

    public function enable(
        string $pluginKey,
        int $tenantId,
        ?CarbonInterface $startsAt = null,
        ?CarbonInterface $endsAt = null,
        array $limits = [],
    ): TenantPlugin {
        $this->ensurePluginExists($pluginKey);

        return DB::transaction(function () use ($pluginKey, $tenantId, $startsAt, $endsAt, $limits) {
            $plugin = TenantPlugin::query()->firstOrNew([
                'tenant_id' => $tenantId,
                'plugin_key' => $pluginKey,
            ]);

            $plugin->enabled = true;

            if ($startsAt !== null) {
                $plugin->starts_at = $startsAt;
            }

            if ($endsAt !== null) {
                $plugin->ends_at = $endsAt;
            }

            if ($limits !== []) {
                $plugin->limits = $limits;
            }

            $plugin->save();

            return $plugin->fresh();
        });
    }

    public function disable(string $pluginKey, int $tenantId): TenantPlugin
    {
        $this->ensurePluginExists($pluginKey);

        return DB::transaction(function () use ($pluginKey, $tenantId) {
            $plugin = TenantPlugin::query()->firstOrNew([
                'tenant_id' => $tenantId,
                'plugin_key' => $pluginKey,
            ]);

            $plugin->enabled = false;
            $plugin->save();

            return $plugin->fresh();
        });
    }

    public function upgrade(string $pluginKey, string $fromVersion, string $toVersion): PluginRegistry
    {
        $registry = $this->ensurePluginExists($pluginKey);

        return DB::transaction(function () use ($registry, $pluginKey, $fromVersion, $toVersion) {
            if ($registry->version !== $fromVersion) {
                throw new PluginLifecycleException("Plugin [$pluginKey] expected version [$fromVersion], found [{$registry->version}].");
            }

            $registry->version = $toVersion;
            $registry->status = PluginRegistry::STATUS_INSTALLED;
            $registry->save();

            $this->recordMigration($pluginKey, $toVersion, 'up');

            return $registry->fresh();
        });
    }

    public function rollback(string $pluginKey, string $fromVersion, string $toVersion): PluginRegistry
    {
        $registry = $this->ensurePluginExists($pluginKey);

        return DB::transaction(function () use ($registry, $pluginKey, $fromVersion, $toVersion) {
            if ($registry->version !== $fromVersion) {
                throw new PluginLifecycleException("Plugin [$pluginKey] expected version [$fromVersion], found [{$registry->version}].");
            }

            $registry->version = $toVersion;
            $registry->status = PluginRegistry::STATUS_INSTALLED;
            $registry->save();

            $this->recordMigration($pluginKey, $toVersion, 'down');

            return $registry->fresh();
        });
    }

    private function recordMigration(string $pluginKey, string $version, string $direction): void
    {
        $batch = $this->nextBatchNumber($pluginKey);

        PluginMigration::query()->create([
            'plugin_key' => $pluginKey,
            'version' => $version,
            'migration_batch' => $batch,
            'direction' => $direction,
            'applied_at' => now(),
            'triggered_by_user_id' => $this->resolveTriggeredByUserId(),
            'correlation_id' => $this->resolveCorrelationId(),
        ]);
    }

    private function nextBatchNumber(string $pluginKey): int
    {
        $lastBatch = PluginMigration::query()
            ->where('plugin_key', $pluginKey)
            ->max('migration_batch');

        return ((int) $lastBatch) + 1;
    }

    private function ensurePluginExists(string $pluginKey): PluginRegistry
    {
        $registry = PluginRegistry::query()->where('plugin_key', $pluginKey)->first();

        if (! $registry) {
            throw new PluginLifecycleException("Plugin [$pluginKey] is not installed.");
        }

        return $registry;
    }

    private function resolveTriggeredByUserId(): ?int
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        $id = method_exists($user, 'getAuthIdentifier') ? $user->getAuthIdentifier() : null;
        if ($id === null || $id === '') {
            return null;
        }

        return is_numeric($id) ? (int) $id : null;
    }

    private function resolveCorrelationId(): ?string
    {
        if (! app()->bound('correlation_id')) {
            return null;
        }

        $value = app('correlation_id');

        return is_string($value) && $value !== '' ? $value : null;
    }
}
